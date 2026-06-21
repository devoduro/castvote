<?php

namespace App\Http\Middleware;

use App\Models\Event;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VotingWindowOpen
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug  = $request->route('slug');
        $event = $slug ? Event::where('slug', $slug)->first() : null;

        if (!$event || !$event->isLive()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Voting is not currently open.'], 403);
            }
            return redirect()->route('vote.index')
                ->with('error', 'Voting for this event is not currently open.');
        }

        return $next($request);
    }
}
