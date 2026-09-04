<?php

namespace Tests;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Event;
use App\Models\Nominee;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Fixture helpers — build the minimum viable voting event in one call
    // -------------------------------------------------------------------------

    protected function createAwardEvent(array $overrides = []): Event
    {
        $org = Organization::factory()->create();

        return Event::factory()->create(array_merge([
            'organization_id' => $org->id,
            'status'          => 'live',
            'starts_at'       => now()->subHour(),
            'ends_at'         => now()->addHours(48),
            'ussd_short_id'   => '240',
            'voting_rules'    => [
                'pay_per_vote'              => true,
                'price_per_vote_pesewas'    => 100,
                'max_votes_per_voter'       => null,
                'requires_eligibility_list' => false,
                'anonymous_tally'           => false,
            ],
        ], $overrides));
    }

    protected function createCategoryWithNominees(Event $event, int $nomineeCount = 3): Category
    {
        $category = Category::factory()->create([
            'event_id'      => $event->id,
            'code'          => '01',
            'display_order' => 1,
        ]);

        for ($i = 1; $i <= $nomineeCount; $i++) {
            Nominee::factory()->create([
                'category_id'   => $category->id,
                'code'          => str_pad($i, 2, '0', STR_PAD_LEFT),
                'display_order' => $i,
            ]);
        }

        return $category;
    }

    protected function actingAsAdmin(Admin $admin = null): static
    {
        $admin ??= Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        return $this;
    }

    // -------------------------------------------------------------------------
    // Arkesel fixture payloads
    // -------------------------------------------------------------------------

    protected function arkeselPayload(
        string $sessionId,
        string $serviceCode,
        string $phone,
        string $text = '',
    ): array {
        return [
            'sessionId'   => $sessionId,
            'serviceCode' => $serviceCode,
            'phoneNumber' => $phone,
            'text'        => $text,
        ];
    }

    // -------------------------------------------------------------------------
    // Paystack fixture payloads
    // -------------------------------------------------------------------------

    protected function paystackChargeSuccessPayload(
        string $reference,
        int    $amountPesewas,
        string $phone = '0244123456',
    ): array {
        return [
            'event' => 'charge.success',
            'data'  => [
                'reference'       => $reference,
                'amount'          => $amountPesewas,
                'currency'        => 'GHS',
                'status'          => 'success',
                'paid_at'         => now()->toIso8601String(),
                'channel'         => 'mobile_money',
                'customer'        => [
                    'email' => $phone . '@ussd.clickvote.placeholder',
                    'phone' => $phone,
                ],
                'metadata'        => [],
            ],
        ];
    }

    protected function paystackChargeFailedPayload(string $reference): array
    {
        return [
            'event' => 'charge.failed',
            'data'  => [
                'reference' => $reference,
                'amount'    => 100,
                'status'    => 'failed',
                'message'   => 'Declined',
            ],
        ];
    }

    protected function paystackSignature(string $payload): string
    {
        return hash_hmac('sha512', $payload, config('services.paystack.secret'));
    }

    protected function postPaystackWebhook(array $payload): \Illuminate\Testing\TestResponse
    {
        $body      = json_encode($payload);
        $signature = $this->paystackSignature($body);

        return $this->withHeaders(['X-Paystack-Signature' => $signature])
            ->postJson('/api/webhooks/paystack', $payload);
    }
}
