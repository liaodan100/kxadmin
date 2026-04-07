<?php

namespace KxAdmin\Controllers;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use KxAdmin\Menus\Menu;
use KxAdmin\Response\ApiResponse;
use KxAdmin\Validate\LoginValidate;

class LoginController extends Controller
{
    use ApiResponse;
    public function loginWithPassword(LoginValidate $request)
    {
        try {
            $params = $request->validated();
            // 使用JWT兼容的用户模型
            $model = resolve(config('admin.admin_model'));
            $hasUser = $model->where('account', $params['username'])->first();
            if (!$hasUser) {
                return $this->error([], '用户不存在');
            }
            if (!password_verify($params['password'], $hasUser->password)) {
                return $this->error([], '密码错误');
            }
            return $this->success([
                'token' => Auth::guard('admin')->login($hasUser),
                'refreshToken' => '',
            ]);
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }

    public function getLoginUserinfo(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->user();
            return $this->success($user);
        } catch (\Exception $e) {
            return $this->error([], $e->getMessage());
        }
    }
}
