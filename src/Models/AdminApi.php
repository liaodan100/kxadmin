<?php

namespace KxAdmin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use KxAdmin\Models\Concerns\HasDateTimeFormat;

class AdminApi extends Model
{
    use HasDateTimeFormat;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'method',
        'path',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function getTable()
    {
        return config('admin.tables.apis', 'admin_apis');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            AdminPermission::class,
            config('admin.tables.permission_apis', 'admin_api_permission'),
            'api_id',
            'permission_id'
        );
    }

    public function scopeEnabled($query)
    {
        return $query->where('status', true);
    }

    public static function normalizePath(string $path): string
    {
        return trim($path, '/');
    }
}
