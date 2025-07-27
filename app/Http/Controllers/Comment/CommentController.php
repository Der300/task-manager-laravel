<?php

namespace App\Http\Controllers\Comment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskCommented;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            return redirect()->back()->with('success', 'Comment added!');
        } catch (Exception $e) {
            DB::rollback();

            return back()->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'body' => 'required|string|max:1000',
        ]);
        dd('ok');
        $comment = Comment::findOrFail($id);
        $comment->body = $request->input('body');
        $comment->save();

        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }
}
