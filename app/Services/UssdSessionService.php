<?php

namespace App\Services;

use App\Jobs\InitiatePaystackCharge;
use App\Models\Category;
use App\Models\Event;
use App\Models\EligibleVoter;
use App\Models\Nominee;
use App\Models\Payment;
use App\Models\UssdSession;
use App\Ussd\UssdResponse;
use App\Ussd\UssdState;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UssdSessionService
{
    private const TTL = 120; // seconds — USSD sessions die fast

    // -------------------------------------------------------------------------
    // Session persistence (Redis primary, DB mirror for debugging/audit)
    // -------------------------------------------------------------------------

    public function load(string $sessionId, string $serviceCode): UssdState
    {
        $raw = Cache::get($this->key($sessionId));

        if ($raw) {
            $arr = json_decode($raw, true);
            return new UssdState(
                sessionId:   $arr['sessionId'],
                serviceCode: $arr['serviceCode'],
                eventId:     $arr['eventId'],
                step:        $arr['step'],
                data:        $arr['data'] ?? [],
            );
        }

        // Extract short ID from full dial string e.g. *928*240# → 240
        $shortId = rtrim($serviceCode, '#');
        $shortId = str_contains($shortId, '*') ? substr(strrchr($shortId, '*'), 1) : $shortId;

        // First hit — resolve event from serviceCode
        $event = Event::where('ussd_short_id', $shortId)
                      ->where('status', 'live')
                      ->first();

        $state = UssdState::fresh($sessionId, $serviceCode, $event?->id);
        $this->save($state);

        // Mirror to DB for debug visibility
        UssdSession::create([
            'arkesel_session_id' => $sessionId,
            'event_id'           => $event?->id,
            'phone_number'       => '',   // filled in by controller on first step
            'current_step'       => 'welcome',
            'payload'            => [],
            'status'             => 'active',
        ]);

        return $state;
    }

    private function save(UssdState $state): void
    {
        Cache::put($this->key($state->sessionId), json_encode($state->toArray()), self::TTL);
    }

    private function key(string $sessionId): string
    {
        return 'ussd:session:' . $sessionId;
    }

    private function advance(UssdState $state, string $nextStep, array $extraData = []): void
    {
        $state->step = $nextStep;
        $state->data = array_merge($state->data, $extraData);
        $this->save($state);

        // Keep DB mirror in sync
        UssdSession::where('arkesel_session_id', $state->sessionId)
            ->update(['current_step' => $nextStep, 'payload' => $state->data]);
    }

    private function terminate(UssdState $state, string $dbStatus = 'completed'): void
    {
        Cache::forget($this->key($state->sessionId));
        UssdSession::where('arkesel_session_id', $state->sessionId)
            ->update(['status' => $dbStatus]);
    }

    // -------------------------------------------------------------------------
    // Menu handlers — pure (state, input) → UssdResponse transitions
    // -------------------------------------------------------------------------

    public function menuWelcome(UssdState $state): UssdResponse
    {
        if (!$state->eventId) {
            $this->terminate($state, 'expired');
            return UssdResponse::end("Sorry, no active voting event found for this shortcode. Please try again later.");
        }

        $event = Event::find($state->eventId);

        if (!$event || !$event->isLive()) {
            $this->terminate($state, 'expired');
            return UssdResponse::end("Voting for {$event?->name} is not currently open. Thank you.");
        }

        $this->advance($state, 'category');

        return UssdResponse::continue(
            "Welcome to {$event->name}\n" .
            "1. Vote\n" .
            "2. Check my vote status\n" .
            "0. Exit"
        );
    }

    public function menuCategory(UssdState $state, ?string $input): UssdResponse
    {
        if ($input === '0') {
            $this->terminate($state);
            return UssdResponse::end("Thank you. Goodbye!");
        }

        if ($input === '2') {
            $this->advance($state, 'status');
            return $this->menuStatus($state);
        }

        if (empty($state->data['awaiting_category'])) {
            // First entry to this step — show category list
            $event      = Event::find($state->eventId);
            $categories = Category::where('event_id', $state->eventId)
                ->orderBy('display_order')
                ->get(['id', 'name', 'code']);

            if ($categories->isEmpty()) {
                $this->terminate($state, 'expired');
                return UssdResponse::end("No categories available yet. Please check back later.");
            }

            $menu = "Enter Category Code:\n";
            foreach ($categories as $cat) {
                $menu .= "{$cat->code}. {$cat->name}\n";
            }
            $menu = rtrim($menu);

            $this->advance($state, 'category', ['awaiting_category' => true]);
            return UssdResponse::continue($menu);
        }

        // Validate category code input
        $categoryCode = trim((string) $input);
        $category = Category::where('event_id', $state->eventId)
            ->where('code', $categoryCode)
            ->first();

        if (!$category) {
            return UssdResponse::continue(
                "Invalid category code '{$categoryCode}'.\n" .
                "Please enter a valid category code or 0 to exit."
            );
        }

        $this->advance($state, 'nominee', [
            'category_id'   => $category->id,
            'category_name' => $category->name,
            'category_code' => $categoryCode,
            'awaiting_category' => false,
        ]);

        $nominees = Nominee::where('category_id', $category->id)
            ->orderBy('display_order')
            ->get(['id', 'name', 'code']);

        $menu = "Category: {$category->name}\nEnter Nominee Code:\n";
        foreach ($nominees as $nom) {
            $menu .= "{$nom->code}. {$nom->name}\n";
        }
        $menu = rtrim($menu);

        return UssdResponse::continue($menu);
    }

    public function menuNominee(UssdState $state, ?string $input): UssdResponse
    {
        if ($input === '0') {
            $this->terminate($state);
            return UssdResponse::end("Thank you. Goodbye!");
        }

        $nomineeCode = trim((string) $input);
        $nominee = Nominee::where('category_id', $state->data['category_id'])
            ->where('code', $nomineeCode)
            ->first();

        if (!$nominee) {
            return UssdResponse::continue(
                "Invalid nominee code '{$nomineeCode}'.\n" .
                "Please enter a valid nominee code or 0 to exit."
            );
        }

        $event = Event::find($state->eventId);

        $this->advance($state, 'quantity', [
            'nominee_id'   => $nominee->id,
            'nominee_name' => $nominee->name,
            'nominee_code' => $nomineeCode,
        ]);

        if (!$event->isPayPerVote()) {
            // Free vote — skip quantity, go straight to confirm
            $this->advance($state, 'confirm', ['quantity' => 1]);
            return UssdResponse::continue(
                "You are about to cast 1 vote for:\n" .
                "{$nominee->name}\n" .
                "Category: {$state->data['category_name']}\n\n" .
                "Reply 1 to Confirm\n" .
                "Reply 2 to Cancel"
            );
        }

        return UssdResponse::continue(
            "Nominee: {$nominee->name}\n" .
            "Category: {$state->data['category_name']}\n" .
            "1 vote = GHS {$event->priceInGhs()}\n\n" .
            "How many votes? (1-50)"
        );
    }

    public function menuQuantity(UssdState $state, ?string $input): UssdResponse
    {
        if ($input === '0') {
            $this->terminate($state);
            return UssdResponse::end("Thank you. Goodbye!");
        }

        $qty = (int) trim((string) $input);

        if ($qty < 1 || $qty > 50 || !is_numeric(trim((string) $input))) {
            return UssdResponse::continue(
                "Please enter a number between 1 and 50.\n" .
                "How many votes?"
            );
        }

        $event  = Event::find($state->eventId);
        $amount = $qty * $event->pricePerVotePesewas();
        $ghs    = number_format($amount / 100, 2);

        $this->advance($state, 'confirm', [
            'quantity'       => $qty,
            'amount_pesewas' => $amount,
        ]);

        return UssdResponse::continue(
            "Confirm your vote:\n" .
            "{$state->data['nominee_name']}\n" .
            "Category: {$state->data['category_name']}\n" .
            "Votes: {$qty} @ GHS {$event->priceInGhs()} each\n" .
            "Total: GHS {$ghs}\n\n" .
            "Reply 1 to Confirm\n" .
            "Reply 2 to Cancel"
        );
    }

    public function menuConfirm(UssdState $state, ?string $input, string $phone): UssdResponse
    {
        if ($input !== '1') {
            $this->terminate($state);
            return UssdResponse::end("Vote cancelled. Thank you for using CastVote.");
        }

        // Detect MoMo network from phone prefix
        $network = $this->detectNetwork($phone);

        // Create pending Payment record (idempotency anchor)
        $reference = 'cv_' . Str::random(24);

        $event    = Event::find($state->eventId);
        $qty      = (int) ($state->data['quantity'] ?? 1);
        $amount   = (int) ($state->data['amount_pesewas'] ?? $qty * $event->pricePerVotePesewas());

        $payment = Payment::create([
            'event_id'           => $state->eventId,
            'provider'           => 'paystack',
            'provider_reference' => $reference,
            'amount_pesewas'     => $amount,
            'currency'           => 'GHS',
            'phone_number'       => $phone,
            'momo_network'       => $network,
            'status'             => 'pending',
            'metadata'           => [
                'nominee_id'  => $state->data['nominee_id'],
                'category_id' => $state->data['category_id'],
                'quantity'    => $qty,
                'channel'     => 'ussd',
            ],
        ]);

        // Dispatch async — do NOT block the USSD response on Paystack round-trip
        InitiatePaystackCharge::dispatch($payment->id);

        $this->terminate($state, 'completed');

        // Update DB session phone
        UssdSession::where('arkesel_session_id', $state->sessionId)
            ->update(['phone_number' => $phone]);

        return UssdResponse::end(
            "Payment request sent!\n" .
            "Approve the MoMo prompt on your phone to confirm your vote for {$state->data['nominee_name']}.\n" .
            "You will receive an SMS confirmation. Thank you!"
        );
    }

    public function menuStatus(UssdState $state): UssdResponse
    {
        // Placeholder — could show last vote cast by this phone
        $this->terminate($state);
        return UssdResponse::end(
            "To check results, visit:\ncastvote.test/events/vote\n" .
            "Thank you for using CastVote."
        );
    }

    public function reset(UssdState $state): UssdResponse
    {
        $this->terminate($state, 'expired');
        return UssdResponse::end("Session expired. Please dial again to vote. Thank you!");
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function detectNetwork(string $phone): string
    {
        // Normalise to local 10-digit format (strip +233 or 233 prefix)
        $local  = preg_replace('/^(\+?233)/', '0', $phone);
        $prefix = substr($local, 0, 3);

        if (in_array($prefix, ['020', '050'])) {
            return 'vodafone';
        }

        if (in_array($prefix, ['026', '056', '027', '057'])) {
            return 'airteltigo';
        }

        // MTN: 024, 054, 055, 059, 025, 053 — and default fallback
        return 'mtn';
    }
}
