<?php

namespace App\Services\File;

class FileService
{
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
}
