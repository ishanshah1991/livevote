<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Exceptions\AlreadyVotedException;
use App\Exceptions\InvalidOptionException;
use App\Exceptions\PollClosedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitVoteRequest;
use App\Models\Poll;
use App\Services\Vote\VoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class VoteController extends Controller
{
    public function __construct(
        private readonly VoteService $voteService,
    ) {}

    public function store(SubmitVoteRequest $request, Poll $poll): JsonResponse
    {
        /** @var int|null $userId */
        $userId = Auth::id();

        try {
            $this->voteService->castVote(
                pollId:    $poll->id,
                optionId:  (int) $request->validated('poll_option_id'),
                userId:    $userId,
                ip:        $request->ip() ?? '0.0.0.0',
                userAgent: $request->userAgent(),
            );
        } catch (AlreadyVotedException) {
            return response()->json([
                'success' => false,
                'message' => 'Already voted',
            ], 409);
        } catch (PollClosedException) {
            return response()->json([
                'success' => false,
                'message' => 'Poll is closed',
            ], 422);
        } catch (InvalidOptionException) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid option',
            ], 422);
        }

        return response()
            ->json([
                'success' => true,
                'message' => 'Vote recorded!',
            ])
            ->cookie('voted_' . $poll->id, '1', 60 * 24 * 7);
    }
}
