<?php

namespace Devhammed\SimpleOrm\Columns;

use Attribute;

/**
 * @template T
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        protected mixed $default = null,
    ) {
    }

    /**
     * @param mixed $value
     * @return T
     */
    public function unserialize($value)
    {
        return $value ?? $this->default;
    }

    /**
     * @param T $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }
}
