<?php

namespace App\Http\Controllers;

use App\Services\UssdSessionService;
use App\Ussd\UssdResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class UssdController extends Controller
{
    public function __construct(private readonly UssdSessionService $sessions) {}

    public function handle(Request $request): Response|JsonResponse
    {
        // Arkesel supports two payload shapes depending on shortcode provisioning:
        //   TEXT mode : { sessionId, serviceCode, phoneNumber, text }
        //   JSON mode : { newSession, sessionId, msisdn, userData, ... }
        // We normalise both into the same variables here.
        [$sessionId, $phone, $serviceCode, $text, $isJsonMode] = $this->normalise($request);

        // text="" on first dial; subsequent steps accumulate inputs separated by *
        $inputs    = ($text === '' || $text === null) ? [] : explode('*', $text);
        $lastInput = count($inputs) > 0 ? end($inputs) : null;
        // end() returns false on empty array; normalise to null
        if ($lastInput === false) {
            $lastInput = null;
        }

        $state = $this->sessions->load($sessionId, $serviceCode);

        // Update the phone on the state's DB mirror for the confirm step
        if ($phone) {
            \App\Models\UssdSession::where('arkesel_session_id', $sessionId)
                ->whereNull('phone_number')
                ->orWhere('phone_number', '')
                ->update(['phone_number' => $phone]);
        }

        $response = match ($state->step) {
            'welcome'  => $this->sessions->menuWelcome($state),
            'category' => $this->sessions->menuCategory($state, $lastInput),
            'nominee'  => $this->sessions->menuNominee($state, $lastInput),
            'quantity' => $this->sessions->menuQuantity($state, $lastInput),
            'confirm'  => $this->sessions->menuConfirm($state, $lastInput, $phone ?? ''),
            default    => $this->sessions->reset($state),
        };

        return $isJsonMode
            ? $this->jsonResponse($response)
            : $this->textResponse($response);
    }

    // -------------------------------------------------------------------------
    // Arkesel payload normalisation
    // -------------------------------------------------------------------------

    private function normalise(Request $request): array
    {
        // JSON mode: Arkesel sends { newSession: bool, sessionId, msisdn, userData }
        if ($request->has('msisdn') || $request->boolean('newSession')) {
            return [
                $request->input('sessionId'),
                $request->input('msisdn'),
                $request->input('serviceCode', $request->input('shortCode', '')),
                $request->input('userData', ''),
                true,
            ];
        }

        // Text mode (standard): { sessionId, serviceCode, phoneNumber, text }
        return [
            $request->input('sessionId'),
            $request->input('phoneNumber'),
            $request->input('serviceCode', ''),
            $request->input('text', ''),
            false,
        ];
    }

    // -------------------------------------------------------------------------
    // Response formatting
    // -------------------------------------------------------------------------

    private function textResponse(UssdResponse $r): Response
    {
        $prefix = $r->isFinal ? 'END' : 'CON';

        return response("{$prefix} {$r->text}", 200)
            ->header('Content-Type', 'text/plain');
    }

    private function jsonResponse(UssdResponse $r): JsonResponse
    {
        return response()->json([
            'continueSession' => !$r->isFinal,
            'message'         => $r->text,
        ]);
    }
}
