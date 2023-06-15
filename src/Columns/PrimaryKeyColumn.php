<?php

namespace Devhammed\SimpleOrm\Columns;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class PrimaryKeyColumn extends Column
{
}
