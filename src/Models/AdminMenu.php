<?php

namespace KxAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use KxAdmin\Models\Concerns\HasDateTimeFormat;

class AdminMenu extends Model
{
    use HasDateTimeFormat;
    use SoftDeletes;

    public const TYPE_CATALOG = 'catalog';
    public const TYPE_MENU = 'menu';
    public const TYPE_BUTTON = 'button';

    protected $fillable = [
        'parent_id',
        'type',
        'path',
        'name',
        'component',
        'route_name',
        'redirect',
        'title',
        'icon',
        'sort',
        'keep_alive',
        'hidden',
        'status',
        'meta',
    ];

    protected $casts = [
        'parent_id' => 'integer',
        'sort' => 'integer',
        'keep_alive' => 'boolean',
        'hidden' => 'boolean',
        'status' => 'boolean',
        'meta' => 'array',
    ];

    public function getTable()
    {
        return config('admin.tables.menus', 'admin_menus');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminRole::class,
            config('admin.tables.role_menus', 'admin_menu_role'),
            'menu_id',
            'role_id'
        );
    }

    public function toRouteArray(): array
    {
        $meta = is_array($this->meta) ? $this->meta : [];
        $meta = array_merge($meta, array_filter([
            'title' => $this->title,
            'icon' => $this->icon,
            'keepAlive' => $this->keep_alive ?: null,
            'isHide' => $this->hidden ?: null,
        ], static fn ($value) => $value !== null && $value !== ''));

        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'type' => $this->type,
            'name' => $this->name,
            'path' => $this->path,
            'component' => $this->component,
            'redirect' => $this->redirect,
            'meta' => $meta,
        ];
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }
}
