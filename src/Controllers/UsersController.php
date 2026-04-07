<?php

namespace KxAdmin\Controllers;

use App\AskPrice\Models\Users;
use App\AskPrice\Requests\UsersRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class UsersController extends AdminController
{
    protected string $model = Users::class;

    protected string $validate = UsersRequest::class;

    public function beforeUpdate(array &$params)
    {
        $params['password'] = bcrypt($params['password']);
    }


}
