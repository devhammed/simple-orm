<?php

namespace Devhammed\SimpleOrm;

use DateTime;

class User extends Model
{
    public function __construct(
        #[Columns\PrimaryKeyColumn]
        public int $id,
        #[Columns\Column]
        public string $name,
        #[Columns\Column]
        public string $email,
        #[Columns\DateTimeColumn]
        public ?DateTime $created_at,
        #[Columns\DateTimeColumn]
        public ?DateTime $updated_at,
    ) {
    }
}
