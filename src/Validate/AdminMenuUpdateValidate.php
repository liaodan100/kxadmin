<?php

namespace KxAdmin\Validate;

class AdminMenuUpdateValidate extends FormValidate
{
    public function rules(): array
    {
        return [
            'parent_id' => ['sometimes', 'integer'],
            'type' => ['sometimes', 'string', 'in:catalog,menu,button'],
            'path' => ['nullable', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:50'],
            'component' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:100'],
            'redirect' => ['nullable', 'string', 'max:255'],
            'title' => ['sometimes', 'string', 'max:100'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort' => ['sometimes', 'integer', 'min:0'],
            'keep_alive' => ['sometimes', 'boolean'],
            'hidden' => ['sometimes', 'boolean'],
            'status' => ['sometimes', 'boolean'],
            'meta' => ['sometimes', 'array'],
        ];
    }
}
