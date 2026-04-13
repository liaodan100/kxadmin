<?php

namespace KxAdmin\Validate;

class LoginValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => '请输入用户名',
            'password.required' => '请输入密码',
        ];
    }
}
