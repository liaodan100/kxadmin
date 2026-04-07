<?php

namespace KxAdmin\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use KxAdmin\Response\ApiResponse;

class UploadController extends Controller
{
    use ApiResponse;
    public function image(Request $request)
    {
        try {
            // 上传至七牛
            $file = $request->file('file');
            $saveAs = $request->post('saveAs');
            $saveAs = $saveAs ?: 'upload/files';

            $mime = $file->getClientMimeType();
            // 允许图片
            if (!in_array($mime, ['image/jpeg', 'image/png', 'image/gif'])) {
                return $this->error([], '上传图片格式错误', 400);
            }
            $ext = $file->getClientOriginalExtension();
            $objectName = $saveAs . '/' . date('Ymd') . md5(uniqid()) . '.' . $ext;
            $result = Storage::disk('qiniu')->put($objectName, file_get_contents($file->getRealPath()));
            if (!$result) {
                return $this->error([], '上传失败', 400);
            }
            return $this->success([
                'url' => Storage::disk('qiniu')->url($objectName),
                'objectName' => $objectName,
            ]);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage(), 400);
        }
    }
}
