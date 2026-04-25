<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Exceptions\PollNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CreatePollRequest;
use App\Services\Poll\PollService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

/**
 * Thin HTTP layer for Poll CRUD — all business logic is delegated to {@see PollService}.
 */
final class PollController extends Controller
{
    public function __construct(
        private readonly PollService $pollService,
    ) {}

    /**
     * List all polls belonging to the authenticated admin, paginated.
     *
     * @return View
     */
    public function index(): View
    {
        $adminId = Auth::guard('admin')->id();
        $polls   = $this->pollService->getAdminPolls($adminId);

        $page      = LengthAwarePaginator::resolveCurrentPage();
        $perPage   = 15;
        $paginator = new LengthAwarePaginator(
            $polls->forPage($page, $perPage)->values(),
            $polls->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath()],
        );

        return view('admin.polls.index', ['polls' => $paginator]);
    }

    /**
     * Render the poll creation form.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.polls.create');
    }

    /**
     * Persist a new poll and redirect to the index.
     *
     * @param  CreatePollRequest  $request
     * @return RedirectResponse
     */
    public function store(CreatePollRequest $request): RedirectResponse
    {
        $adminId = Auth::guard('admin')->id();

        $this->pollService->createPoll($adminId, $request->validated());

        return redirect()
            ->route('admin.polls.index')
            ->with('success', 'Poll created successfully.');
    }

    /**
     * Show poll details and the shareable voting link.
     *
     * @param  int  $id
     * @return View
     */
    public function show(int $id): View
    {
        try {
            $poll = $this->pollService->getPollWithOptions($id);
        } catch (PollNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        $shareableLink = $this->pollService->generateShareableLink($poll);

        return view('admin.polls.show', compact('poll', 'shareableLink'));
    }

    /**
     * Show live vote-count results for a poll.
     *
     * @param  int  $id
     * @return View
     */
    public function results(int $id): View
    {
        try {
            $poll = $this->pollService->getPollResults($id);
        } catch (PollNotFoundException $e) {
            abort(404, $e->getMessage());
        }

        return view('admin.polls.results', compact('poll'));
    }

    /**
     * Toggle a poll's active state; returns JSON for XHR callers.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function toggleActive(int $id): JsonResponse
    {
        $adminId = Auth::guard('admin')->id();

        try {
            $isActive = $this->pollService->togglePollActive($id, $adminId);
        } catch (PollNotFoundException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        } catch (UnauthorizedException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 403);
        }

        return response()->json(['success' => true, 'is_active' => $isActive]);
    }
}
