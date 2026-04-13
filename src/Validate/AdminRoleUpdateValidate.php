<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminRoleUpdateValidate extends FormValidate
{
    public function rules(): array
    {
        $role = $this->route('role');
        $roleId = is_object($role) ? $role->id : $role;

        return [
            'name' => ['sometimes', 'string', 'max:50'],
            'code' => ['sometimes', 'string', 'max:50', Rule::unique(config('admin.tables.roles', 'admin_roles'), 'code')->ignore($roleId)],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
            'permission_ids' => ['sometimes', 'array'],
            'permission_ids.*' => ['integer'],
            'menu_ids' => ['sometimes', 'array'],
            'menu_ids.*' => ['integer'],
        ];
    }
}
