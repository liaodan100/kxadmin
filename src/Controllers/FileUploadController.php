<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Throwable;

class FileUploadController extends AdminController
{
    public function file(Request $request): JsonResponse
    {
        $request->validate($this->rules(false));

        return $this->success($this->storeFile($request, false), '上传成功');
    }

    public function image(Request $request): JsonResponse
    {
        $request->validate($this->rules(true));

        return $this->success($this->storeFile($request, true), '上传成功');
    }

    protected function rules(bool $imageOnly): array
    {
        $availableDisks = array_keys((array) config('filesystems.disks', []));
        $configuredDisks = (array) config('admin.upload.allowed_disks', []);
        $allowedDisks = $configuredDisks === []
            ? $availableDisks
            : array_values(array_intersect($configuredDisks, $availableDisks));
        $maxSize = $imageOnly
            ? (int) config('admin.upload.image_max_size', 5120)
            : (int) config('admin.upload.max_size', 10240);

        return [
            'file' => array_values(array_filter([
                'required',
                'file',
                $imageOnly ? 'image' : null,
                'max:' . max($maxSize, 1),
            ])),
            'disk' => $allowedDisks === []
                ? ['sometimes', 'string']
                : ['sometimes', 'string', 'in:' . implode(',', $allowedDisks)],
            'directory' => ['sometimes', 'string', 'max:255'],
            'filename' => ['sometimes', 'string', 'max:100'],
        ];
    }

    protected function storeFile(Request $request, bool $imageOnly): array
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');
        $disk = (string) $request->input('disk', config('admin.upload.disk', config('filesystems.default', 'public')));
        $defaultDirectory = $imageOnly
            ? (string) config('admin.upload.image_directory', 'admin/uploads/images')
            : (string) config('admin.upload.directory', 'admin/uploads/files');
        $directory = $this->normalizeDirectory((string) $request->input('directory', $defaultDirectory));
        $filename = $this->makeFilename($file, (string) $request->input('filename', ''));

        $path = Storage::disk($disk)->putFileAs($directory, $file, $filename, [
            'visibility' => (string) config('admin.upload.visibility', 'public'),
        ]);

        return [
            'original_name' => $file->getClientOriginalName(),
            'filename' => basename($path),
            'path' => $path,
            'url' => $this->resolveUrl($disk, $path),
            'disk' => $disk,
            'directory' => $directory,
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ];
    }

    protected function normalizeDirectory(string $directory): string
    {
        $directory = str_replace('\\', '/', trim($directory, '/'));

        return $directory === '' ? 'admin/uploads/files' : $directory;
    }

    protected function makeFilename(UploadedFile $file, string $filename): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: '');
        $filename = trim($filename);

        if ($filename === '') {
            $filename = date('YmdHis') . '_' . Str::lower(Str::random(12));
        } else {
            $filename = Str::slug(pathinfo($filename, PATHINFO_FILENAME), '_');
            if ($filename === '') {
                $filename = date('YmdHis') . '_' . Str::lower(Str::random(12));
            }
        }

        return $extension === '' ? $filename : $filename . '.' . $extension;
    }

    protected function resolveUrl(string $disk, string $path): ?string
    {
        try {
            return Storage::disk($disk)->url($path);
        } catch (Throwable) {
            return null;
        }
    }
}
