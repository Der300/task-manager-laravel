<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCommented;
use App\Services\Comment\CommentService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        if ($user->hasAnyRole(['admin', 'super-admin'])) {
            $data = $this->commentService->getCommentsWithPanigation();
        } elseif (Comment::where('user_id', $user->id)->exists()) {

            $data = $this->commentService->getCommentsWithPanigation(['user_id' => $user->id]);
        } else {
            abort(403, 'You have no comments to view.');
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
        $userId = Auth::user()->id;
        $assigneeTask = $task->assigned_to;
        try {
            DB::beginTransaction();
            $comment = Comment::create([
                'task_id' => $task->id,
                'body' => $data['body'],
                'user_id' => $userId,
            ]);


            DB::commit();
            // gửi thông báo
            if ($assigneeTask) {
                User::find($assigneeTask)?->notify(new TaskCommented($comment, $task->id));
            }

            return back()->with('success', 'Comment added!');
        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Comment $comment)
    {
        $this->authorize('update', $comment);
        $data = $request->validated();
        $comment->body = $data['body'];
        $comment->save();

        return response()->json(['success' => true]);
    }


    /**
     * Move to recycle the specified resource from storage.
     */
    public function softDelete(Comment $comment)
    {
        $this->authorize('softDelete', $comment);

        try {
            $comment->delete(); // Soft delete

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
        try {
            if ($comment->trashed()) {
                $comment->restore();
                return redirect()->route('tasks.show', ['task' => $comment->task_id, 'comment_id' => $comment->id])->with('success', 'Comment restored successfully.');
            }
            return back()->with('error', 'Comment is not deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to restore comment.');
        }
    }

    /**
     * Delete comment.
     */
    public function forceDelete(Comment $comment)
    {
        try {
            $comment->forceDelete();
            return back()->with('success', 'Comment has been permanently deleted.');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting comment.');
        }
    }
}
