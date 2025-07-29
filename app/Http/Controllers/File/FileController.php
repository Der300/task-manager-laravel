<?php

namespace App\Http\Controllers\File;

use App\Http\Controllers\Controller;
use App\Http\Requests\File\UploadRequest;
use App\Models\File;
use App\Models\Task;
use App\Notifications\FileDownloaded;
use App\Notifications\FileForceDeleted;
use App\Notifications\FileRestored;
use App\Notifications\FileSoftDeleted;
use App\Notifications\FileUploaded;
use App\Services\File\FileService;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
    public function __construct(protected FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $files = $this->fileService->getAllVisibleFilesForUser(Auth::user());

        return view('myfiles.index', ['files' => $files]);
    }

    public function recycle()
    {
        $files = $this->fileService->getAllVisibleFilesForUserToRecycle(Auth::user());

        return view('myfiles.recycle', ['files' => $files]);
    }

    public function softDelete(File $file)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $file->delete();
            DB::commit();
            $this->fileService->moveFileToTrash($file);
            if ($user->id !== $file->task?->assigned_to) {
                $file->task?->assignedUser->notify(new FileSoftDeleted($file, $file->task?->assignedUser?->name, $user->name));
            }
            return back()->with('success', 'Files moved to recycle.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while move file.');
        }
    }

    public function restore(File $file)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $file->restore();
            DB::commit();
            $this->fileService->restoreFileFromTrash($file);
            if ($user->id !== $file->task?->assigned_to) {
                $file->task?->assignedUser->notify(new FileRestored($file, $file->task?->assignedUser?->name, $user->name));
            }
            return back()->with('success', 'Files restored.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while restore file.');
        }
    }

    public function forceDelete(File $file)
    {
        $user = Auth::user();
        try {
            DB::beginTransaction();
            $file->forceDelete();
            DB::commit();
            $this->fileService->deleteFilePermanently($file);
            if ($user->id !== $file->task?->assigned_to) {
                $file->task?->assignedUser->notify(new FileForceDeleted($file, $file->task?->assignedUser?->name, $user->name));
            }
            return back()->with('success', 'Files permanently deleted.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while delete file.');
        }
    }

    public function upload(UploadRequest $request, Task $task)
    {
        $user = Auth::user();

        try {
            DB::beginTransaction();
            $file = $this->fileService->uploadFile($task->id, $request->file('file'), $request->description, $user->id);
            DB::commit();

            if ($user->id !== $task->assigned_to) {
                $task->assignedUser->notify(new FileUploaded($file, $task->assignedUser?->name, $user->name));
            }
            return response()->json([
                'message' => 'File uploaded!',
                'file' => [
                    'id' => $file->id,
                    'original_name' => $file->original_name,
                    'mime_type' => $file->mime_type,
                    'description' => $file->description,
                    'uploader_name' => $file->uploader?->name,
                    'created_at' => $file->created_at->format('d/m/Y H:i'),
                    'updated_at' => $file->updated_at->format('d/m/Y H:i'),
                    'path' => $file->path,
                ]
            ]);
        } catch (Exception $e) {
        }
    }

    public function download(File $file)
    {
        $user = Auth::user();
        if ($user->id !== $file->task?->assigned_to) {
            $file->task?->assignedUser->notify(new FileDownloaded($file, $file->task?->assignedUser?->name, $user->name));
        }
        return $this->fileService->downloadFile($file);
    }
}
