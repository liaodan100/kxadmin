<?php

namespace KxAdmin\Validate;

use Illuminate\Validation\Rule;

class AdminApiStoreValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'method' => ['required', 'string', 'max:10'],
            'path' => ['required', 'string', 'max:255', Rule::unique(config('admin.tables.apis', 'admin_apis'), 'path')->where(fn ($query) => $query->where('method', strtoupper((string) $this->input('method'))))],
            'description' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', 'boolean'],
        ];
    }
}
