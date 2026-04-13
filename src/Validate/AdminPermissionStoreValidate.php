<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminPermissionStoreValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:50'],
            'code' => ['required', 'string', 'max:100', Rule::unique(config('admin.tables.permissions', 'admin_permissions'), 'code')],
            'group' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'api_ids' => ['sometimes', 'array'],
            'api_ids.*' => ['integer'],
        ];
    }
}
