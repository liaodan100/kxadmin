<?php

namespace KxAdmin\Models\Concerns;

use DateTimeInterface;

trait HasDateTimeFormat
{
    public function getDateFormat()
    {
        return 'Y-m-d H:i:s';
    }

    protected function serializeDate(DateTimeInterface $date): string
    {
        return $date->format($this->getDateFormat());
    }
}
