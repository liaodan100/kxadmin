<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminUserStoreValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:50', Rule::unique(config('admin.tables.users', 'admin_users'), 'username')],
            'password' => ['required', 'string', 'min:6', 'max:100'],
            'name' => ['required', 'string', 'max:50'],
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
