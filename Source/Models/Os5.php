<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os5 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os5", ["id_emp2", "id_os1", "id_os2"], "id");
    }

    public function findByIdOs($id_os, $columns = "*"){
        $find = $this->find("id_os1 = :id_os1", "id_os1={$id_os}", $columns, false);
        return $find->fetch(true);
    }
}
