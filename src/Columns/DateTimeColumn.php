<?php

namespace Devhammed\SimpleOrm\Columns;

use DateTime;
use Attribute;
use Exception;

#[Attribute(Attribute::TARGET_PROPERTY)]
class DateTimeColumn extends Column
{
    public function __construct(
        protected mixed $default = null,
        protected string $format = 'Y-m-d H:i:s',
    ) {
        parent::__construct(default: $default);
    }

    public function unserialize(mixed $value): ?DateTime
    {
        if ($value === null) {
            return $this->default;
        }

        if ($value instanceof DateTime) {
            return $value;
        }

        if (!is_string($value)) {
            return $this->default;
        }

        $date = DateTime::createFromFormat($this->format, $value);

        if (!$date) {
            return $this->default;
        }

        return $date;
    }

    public function serialize(mixed $value): string
    {
        if ($value instanceof DateTime) {
            $value = $value->format($this->format);
        }

        if (!is_string($value)) {
            throw new Exception('Invalid date time format.');
        }

        return $value;
    }
}
