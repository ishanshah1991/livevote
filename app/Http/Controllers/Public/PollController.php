<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\Vote;
use App\Services\Poll\PollService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class PollController extends Controller
{
    public function __construct(
        private readonly PollService $pollService,
    ) {}

    public function show(Poll $poll): View
    {
        if (! $this->isPollOpen($poll)) {
            $poll = $this->pollService->getPollResults($poll->id);

            return view('poll.closed', compact('poll'));
        }

        $hasVoted = session()->has('voted_polls.' . $poll->id);

        if ($hasVoted) {
            $poll = $this->pollService->getPollResults($poll->id);
        } else {
            $poll = $this->pollService->getPollWithOptions($poll->id);
        }

        return view('poll.show', compact('poll', 'hasVoted'));
    }

    public function results(Poll $poll): View
    {
        $poll = $this->pollService->getPollResults($poll->id);

        return view('poll.results', compact('poll'));
    }

    public function vote(Poll $poll, Request $request): JsonResponse
    {
        if (! $this->isPollOpen($poll)) {
            return response()->json(['error' => 'This poll is no longer accepting votes.'], 422);
        }

        if (session()->has('voted_polls.' . $poll->id)) {
            return response()->json(['error' => 'already_voted'], 409);
        }

        $validated = $request->validate([
            'option_id' => [
                'required',
                'integer',
                Rule::exists('poll_options', 'id')->where('poll_id', $poll->id),
            ],
        ]);

        try {
            DB::transaction(function () use ($poll, $validated, $request): void {
                Vote::create([
                    'poll_id'        => $poll->id,
                    'poll_option_id' => (int) $validated['option_id'],
                    'ip_address'     => $request->ip() ?? '0.0.0.0',
                    'user_agent'     => $request->userAgent(),
                    'voted_at'       => Carbon::now(),
                ]);

                PollOption::where('id', $validated['option_id'])->increment('votes_count');
            });
        } catch (QueryException $e) {
            // PostgreSQL unique violation (23505) — same IP already voted
            if (($e->errorInfo[0] ?? '') === '23000' || ($e->errorInfo[0] ?? '') === '23505') {
                return response()->json(['error' => 'already_voted'], 409);
            }

            throw $e;
        }

        session()->put('voted_polls.' . $poll->id, true);

        $poll = $this->pollService->getPollResults($poll->id);
        $total = $poll->options->sum('votes_count');

        return response()->json([
            'total'   => $total,
            'results' => $poll->options->map(fn ($opt) => [
                'id'         => $opt->id,
                'text'       => $opt->option_text,
                'votes'      => $opt->votes_count,
                'percentage' => $total > 0 ? round(($opt->votes_count / $total) * 100, 1) : 0.0,
            ]),
        ]);
    }

    private function isPollOpen(Poll $poll): bool
    {
        if (! $poll->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($poll->starts_at !== null && $poll->starts_at->gt($now)) {
            return false;
        }

        if ($poll->ends_at !== null && $poll->ends_at->lt($now)) {
            return false;
        }

        return true;
    }
}
