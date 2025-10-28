<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os2_1 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os2_1", ["id_emp2", "id_os1", "id_os2"], "id");
    }

    public function findByIdOs($id_os, $columns = "*"){
        $find = $this->find("id_os1 = :id_os1", "id_os1={$id_os}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }

    public function findByOs2($id_os2, $columns = "*"){
        $find = $this->find("id_os2 = :id_os2", "id_os2={$id_os2}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }
}
