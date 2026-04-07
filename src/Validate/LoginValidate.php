<?php

namespace KxAdmin\Validate;

class LoginValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'username' => 'required|max:16',
            'password' => 'required|max:32|min:6',
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '用户名不能为空',
            'username.max' => '用户名不能超过16个字符',
            'password.required' => '密码不能为空',
            'password.max' => '密码不能超过32个字符',
            'password.min' => '密码不能少于6个字符',
        ];
    }
}
