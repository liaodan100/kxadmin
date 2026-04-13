<?php

namespace KxAdmin\Support;

use Illuminate\Support\Collection;

class MenuTreeBuilder
{
    public static function build(Collection $menus, int $parentId = 0, ?callable $transform = null): array
    {
        $grouped = $menus->groupBy(fn ($menu) => (int) ($menu->parent_id ?? 0));
        $transform ??= static fn ($menu): array => $menu->toArray();

        return self::branch($grouped, $parentId, $transform);
    }

    protected static function branch(Collection $grouped, int $parentId, callable $transform): array
    {
        return $grouped
            ->get($parentId, collect())
            ->sortBy([
                ['sort', 'asc'],
                ['id', 'asc'],
            ])
            ->values()
            ->map(function ($menu) use ($grouped, $transform): array {
                $node = $transform($menu);
                $node['children'] = self::branch($grouped, (int) $menu->id, $transform);

                return $node;
            })
            ->all();
    }
}
