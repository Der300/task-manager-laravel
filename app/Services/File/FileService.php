<?php

namespace App\Services\File;

use App\Models\File;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileService
{
    protected string $folder = 'files/';
    protected string $trashFolder = 'files/trash/';

    function getFileTypeLabel(string $mime): string
    {
        return match (true) {
            str_starts_with($mime, 'image/')            => 'Image',
            str_starts_with($mime, 'video/')            => 'Video',
            str_starts_with($mime, 'audio/')            => 'Audio',
            str_starts_with($mime, 'application/pdf')   => 'PDF',
            str_contains($mime, 'word')                 => 'Word',
            str_contains($mime, 'excel')                => 'Excel',
            str_contains($mime, 'powerpoint')
                || str_contains($mime, 'presentation')  => 'PowerPoint',
            str_starts_with($mime, 'text/plain')        => 'Text File',
            str_contains($mime, 'zip')
                || str_contains($mime, 'rar')
                || str_contains($mime, 'compressed')    => 'Archive',
            default => 'Other',
        };
    }

    /**
     * Move file to Trash
     * 
     * @param File $file
     */
    public function moveFileToTrash(File $file): void
    {
        $from = public_path($this->folder . $file->stored_name);
        $to = public_path($this->trashFolder . $file->stored_name);

        $trashDir = dirname($to);
        if (!is_dir($trashDir)) {
            mkdir($trashDir, 0755, true);
        }

        if (file_exists($from)) {
            rename($from, $to);
            $file->update(['path' => $this->trashFolder . $file->stored_name]);
        }
    }

    /**
     * Restore file
     * 
     * @param File $file
     */
    public function restoreFileFromTrash(File $file): void
    {
        $from = public_path($this->trashFolder . $file->stored_name);
        $to = public_path($this->folder . $file->stored_name);

        if (file_exists($from)) {
            rename($from, $to);
            $file->update(['path' => $this->folder . $file->stored_name]);
        }
    }

    /**
     * Delete file
     * 
     * @param File $file
     */
    public function deleteFilePermanently(File $file): void
    {
        $filePath = public_path($file->path);
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $file->forceDelete();
    }

    /**
     * Di chuyển files liên quan task vào trash
     * 
     * @param string $taskId
     */
    public function moveTaskFilesToTrash(string $taskId): void
    {
        $files = File::where('task_id', $taskId)->get();

        foreach ($files as $file) {
            $this->moveFileToTrash($file);
        }
    }

    /**
     * Restore files liên quan task
     * 
     * @param Task $task tên task
     */
    public function restoreTaskFilesFromTrash(string $taskId): void
    {
        $files = File::where('task_id', $taskId)->get();

        foreach ($files as $file) {
            $this->restoreFileFromTrash($file);
        }
    }

    /**
     * Delete files liên quan task
     * 
     * @param string $taskId tên task
     */
    public function deleteTaskFilesPermanently(string $taskId): void
    {
        $files = File::where('task_id', $taskId)->get();

        foreach ($files as $file) {
            $this->deleteFilePermanently($file);
        }
    }

    public function uploadFile(string $taskId, UploadedFile $file, ?string $description = null, ?string $uploaderId = null): File
    {
        $storedName = uniqid() . '_' . $file->getClientOriginalName();
        $folderPath = public_path($this->folder);

        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0755, true);
        }

        $file->move($folderPath, $storedName);

        return File::create([
            'original_name' => $file->getClientOriginalName(),
            'stored_name'   => $storedName,
            'mime_type'     => $file->getClientMimeType(),
            'path'          => $this->folder . $storedName,
            'task_id'       => $taskId,
            'uploaded_by'   => $uploaderId,
            'description'   => $description,
        ]);
    }

    public function downloadFile(File $file): BinaryFileResponse
    {
        $path = public_path($file->path);
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->download($path, $file->original_name);
    }

    public function getFilesByTaskId(int $taskId)
    {
        return File::with('uploader:id,name', 'task:id')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function baseVisibleFilesForUser(User $user)
    {
        $query = File::with(['task.project']);

        if ($user->hasRole('client')) {
            $query->whereHas('task.project', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            });
        } elseif ($user->hasRole('member')) {
            $query->whereHas('task', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } elseif ($user->hasRole('leader')) {
            $query->whereHas('task.project.tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        } elseif ($user->hasRole('manager')) {
            $query->whereHas('task.project', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function getAllVisibleFilesForUser(User $user): LengthAwarePaginator
    {
        return $this->baseVisibleFilesForUser($user)->paginate(env('ITEM_PER_PAGE', 10));
    }

    public function getAllVisibleFilesForUserToRecycle(User $user): LengthAwarePaginator
    {
        return $this->baseVisibleFilesForUser($user)->onlyTrashed()->paginate(env('ITEM_PER_PAGE', 10));
    }
}
