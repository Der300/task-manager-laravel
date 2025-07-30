<?php

namespace App\Http\Controllers\Comment;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Services\Comment\CommentService;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\CommentAdded;
use App\Notifications\CommentForceDeleted;
use App\Notifications\CommentRestored;
use Exception;
use App\Notifications\CommentSoftDeleted;
use App\Notifications\CommentUpdated;

class CommentController extends Controller
{
    use AuthorizesRequests;
    protected $commentService;
    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $data = null;
        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $data = $this->commentService->getCommentsWithPagination();
        } else {
            $data = $this->commentService->getCommentsWithPagination($user->id);
        }
        return view('comments.index', ['data' => $data]);
    }

    public function recycle()
    {
        $user = Auth::user();

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $data = $this->commentService->getDataCommentRecycleTable();
        } else {
            $data = $this->commentService->getDataCommentRecycleTable($user->id);
        }

        return view('comments.recycle', ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Task $task)
    {
        $data = $request->validated();
        $user = Auth::user();
        $assigneeId = $task->assigned_to;
        try {
            DB::beginTransaction();
            $comment = Comment::create([
                'task_id' => $task->id,
                'body' => $data['body'],
                'user_id' => $user->id,
            ]);

            DB::commit();
            // gửi thông báo nếu người gửi khác assinged_to của task
            if ($assigneeId && $assigneeId !== $user->id) {
                $task->assignedUser?->notify(new CommentAdded($comment, $task->id, $user->name));
            }

            return redirect()->route('tasks.show', ['task' => $task->id])->with('success', 'Comment added!');
        } catch (Exception $e) {
            DB::rollback();

            return redirect()->route('tasks.show', ['task' => $task->id])->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $user = Auth::user();
        $this->authorize('update', $comment);
        $data = $request->validated();

        $comment->body = $data['body'];
        $comment->save();

        if ($user->id !== $comment->user_id) {
            $comment->user?->notify(new CommentUpdated($comment, $comment->task_id, $user->name));
        }

        return response()->json(['success' => true]);
    }


    /**
     * Move to recycle the specified resource from storage.
     */
    public function softDelete(Comment $comment)
    {
        $this->authorize('softDelete', $comment);
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $comment->delete(); // Soft delete
            DB::commit();
            if ($user->id !== $comment->user_id) {
                // elequent lấy user dựa theo quan hệ user_id
                $comment->user?->notify(new CommentSoftDeleted($comment, $user->name));
            }

            return back()->with('success', 'Comment moved to recycle successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to move comment to recycle.');
        }
    }

    /**
     * Restore the specified resource from storage.
     */
    public function restore(Comment $comment)
    {
        $this->authorize('restore', $comment);
        $user = Auth::user();
        try {
            if ($comment->trashed()) {
                $comment->restore();
                if ($user->id !== $comment->user_id) {
                    $comment->user?->notify(new CommentRestored($comment, $user->name));
                }
                return redirect()->route('tasks.show', ['task' => $comment->task_id, 'comment_id' => $comment->id])->with('success', 'Comment restored successfully.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore comment.');
        }
    }

    /**
     * Delete comment.
     */
    public function forceDelete(Comment $comment)
    {
        $user = Auth::user();
        try {
            $comment->forceDelete();
            if ($user->id !== $comment->user_id) {
                $comment->user?->notify(new CommentForceDeleted($comment, $user->name));
            }
            return back()->with('success', 'Comment has been permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting comment.');
        }
    }
}
