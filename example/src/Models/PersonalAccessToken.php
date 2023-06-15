<?php

namespace Devhammed\SimpleOrmExample\Models;

use DateTime;
use Devhammed\SimpleOrm\Model;
use Devhammed\SimpleOrm\Columns;

class PersonalAccessToken extends Model
{
    public function __construct(
        #[Columns\PrimaryKeyColumn]
        public int $id,
        #[Columns\Column]
        public int $tokenable_id,
        #[Columns\Column]
        public string $tokenable_type,
        #[Columns\Column]
        public string $token,
        #[Columns\Column]
        public string $name,
        #[Columns\DateTimeColumn]
        public ?DateTime $last_used_at,
        #[Columns\DateTimeColumn]
        public ?DateTime $created_at,
        #[Columns\DateTimeColumn]
        public ?DateTime $updated_at,
    ) {
    }
}
