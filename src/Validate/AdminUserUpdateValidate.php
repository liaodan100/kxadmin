<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminUserUpdateValidate extends FormValidate
{
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'username' => ['sometimes', 'string', 'max:50', Rule::unique(config('admin.tables.users', 'admin_users'), 'username')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:6', 'max:100'],
            'name' => ['sometimes', 'string', 'max:50'],
            'avatar' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:100'],
            'mobile' => ['nullable', 'string', 'max:20'],
            'status' => ['sometimes', 'boolean'],
            'is_super' => ['sometimes', 'boolean'],
            'role_ids' => ['sometimes', 'array'],
            'role_ids.*' => ['integer'],
        ];
    }
}
