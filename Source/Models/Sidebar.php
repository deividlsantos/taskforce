<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Sidebar extends DataLayer
{
    public function __construct()
    {
        parent::__construct("sidebar", ["id_emp2", "id_users"]);
    }

    public function findByUser(int $id_emp2, int $id_users, string $columns = "*"): ?array
    {
        return $this->find("id_emp2 = :id_emp2 AND id_users = :id_users", "id_emp2={$id_emp2}&id_users={$id_users}", $columns, false)->fetch(true);
    }
}
