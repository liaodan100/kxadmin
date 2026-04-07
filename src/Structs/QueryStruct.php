<?php

namespace KxAdmin\Structs;

class QueryStruct
{
    public int $current;
    public int $size;
    public string $sort;

    public function __construct(int $current, int $size, string $sort)
    {
        $this->current = $current;
        $this->size = $size;
        $this->sort = $sort;
    }
    public static function load(array $array): QueryStruct
    {
        return new QueryStruct(
            $array['current'] ?? 1,
            $array['size'] ?? 10,
            $array['sort'] ?? ''
        );
    }
}
