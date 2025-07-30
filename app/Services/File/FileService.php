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

    /**
     * Lấy tên loại file
     */
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


    private function moveFile(File $file, string $fromDir, string $toDir): void
    {
        $from = public_path($fromDir . $file->stored_name);
        $to = public_path($toDir . $file->stored_name);

        if (!is_dir(dirname($to))) {
            mkdir(dirname($to), 0755, true);
        }

        if (file_exists($from)) {
            rename($from, $to);
        }
    }

    /**
     * Move file to Trash
     */
    public function moveFileToTrash(File $file): void
    {
        $this->moveFile($file, $this->folder, $this->trashFolder);
        $file->update(['path' => $this->trashFolder . $file->stored_name]);
    }

    /**
     * Restore file
     */
    public function restoreFileFromTrash(File $file): void
    {
        $this->moveFile($file, $this->trashFolder, $this->folder);
        $file->update(['path' => $this->folder . $file->stored_name]);
    }

    /**
     * Delete file
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
     * Move files of Task
     */
    public function moveTaskFilesToTrash(string $taskId): void
    {
        File::where('task_id', $taskId)->get()->each(fn($file) => $this->moveFileToTrash($file));
    }

    /**
     * Restore files of Task
     */
    public function restoreTaskFilesFromTrash(string $taskId): void
    {
        File::where('task_id', $taskId)->get()->each(fn($file) => $this->restoreFileFromTrash($file));
    }

    /**
     * Restore files of Task
     */
    public function deleteTaskFilesPermanently(string $taskId): void
    {
        File::where('task_id', $taskId)->get()->each(fn($file) => $this->deleteFilePermanently($file));
    }

    /**
     * up file
     */
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

    /**
     * down file
     */
    public function downloadFile(File $file): BinaryFileResponse
    {
        $path = public_path($file->path);
        if (!file_exists($path)) {
            abort(404, 'File not found.');
        }

        return response()->download($path, $file->original_name);
    }

    /**
     * Lấy toàn bộ file của task
     */
    public function getFilesByTaskId(int $taskId)
    {
        return File::with('uploader:id,name', 'task')
            ->where('task_id', $taskId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function baseVisibleFilesForUser(User $user, string $role)
    {
        $query = File::with(['task.project']);
        match ($role) {
            'client' => $query->whereHas('task.project', function ($q) use ($user) {
                $q->where('client_id', $user->id);
            }),
            'member' => $query->whereHas('task', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            }),
            'leader' => $query->whereHas('task.project.tasks', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            }),
            'manager' => $query->whereHas('task.project', function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            }),
            default => null,
        };
        return $query->latest('created_at');
    }

    /**
     * Lấy toàn bộ file của user
     */
    public function getAllVisibleFilesForUser(User $user): LengthAwarePaginator
    {
        return $this->baseVisibleFilesForUser($user, $user->role)->paginate(env('ITEM_PER_PAGE', 10));
    }

    /**
     * Lấy toàn bộ file đã bị soft-delete của user
     */
    public function getAllVisibleFilesForUserToRecycle(User $user): LengthAwarePaginator
    {
        return $this->baseVisibleFilesForUser($user, $user->role)->onlyTrashed()->paginate(env('ITEM_PER_PAGE', 10));
    }
}
