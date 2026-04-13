<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminApiUpdateValidate extends FormValidate
{
    public function rules(): array
    {
        $api = $this->route('api');
        $apiId = is_object($api) ? $api->id : $api;

        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'method' => ['sometimes', 'string', 'max:10'],
            'path' => ['sometimes', 'string', 'max:255', Rule::unique(config('admin.tables.apis', 'admin_apis'), 'path')->ignore($apiId)->where(fn ($query) => $query->where('method', strtoupper((string) $this->input('method', optional($api)->method))))],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
