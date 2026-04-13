<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminPermissionUpdateValidate extends FormValidate
{
    public function rules(): array
    {
        $permission = $this->route('permission');
        $permissionId = is_object($permission) ? $permission->id : $permission;

        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'code' => ['sometimes', 'string', 'max:100', Rule::unique(config('admin.tables.permissions', 'admin_permissions'), 'code')->ignore($permissionId)],
            'group' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'api_ids' => ['sometimes', 'array'],
            'api_ids.*' => ['integer'],
        ];
    }
}
