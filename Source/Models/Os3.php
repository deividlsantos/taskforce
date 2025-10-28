<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os3 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os3", ["id_emp2", "id_os1"], "id");
    }

    public function findByIdOs($id_os, $columns = "*")
    {
        $find = $this->find("id_os1 = :id_os1", "id_os1={$id_os}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }
}
