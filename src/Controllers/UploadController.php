<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends AdminController
{
    public function image(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'image', 'max:5120'],
            'disk' => ['sometimes', 'string'],
            'directory' => ['sometimes', 'string'],
        ]);

        $disk = (string) $request->input('disk', config('filesystems.default', 'public'));
        $directory = trim((string) $request->input('directory', 'admin/uploads/images'), '/');
        $path = $request->file('file')->store($directory, $disk);

        return $this->success([
            'path' => $path,
            'url' => Storage::disk($disk)->url($path),
            'disk' => $disk,
        ], '上传成功');
    }
}
