<?php

namespace KxAdmin\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use KxAdmin\Response\ApiResponse;

abstract class AdminController extends Controller
{
    use ApiResponse;

    protected function paginate(Request $request, Builder $query): JsonResponse
    {
        $current = max((int) $request->input('current', 1), 1);
        $size = min(max((int) $request->input('size', 10), 1), 100);
        $total = (clone $query)->count();
        $records = $query->forPage($current, $size)->get();

        return $this->success([
            'records' => $records,
            'total' => $total,
            'current' => $current,
            'size' => $size,
        ]);
    }

    protected function toIdArray(array $ids): array
    {
        return collect($ids)
            ->filter(static fn ($id) => $id !== null && $id !== '')
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }
}
