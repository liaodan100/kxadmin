<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminRoleStoreValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:50', Rule::unique(config('admin.tables.roles', 'admin_roles'), 'code')],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer'],
            'menu_ids' => ['sometimes', 'array'],
            'menu_ids.*' => ['integer'],
        ];
    }
}
