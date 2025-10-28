<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os6 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os6", ["id_emp2", "id_os1", "id_os2"], "id");
    }

    public function findByIdOs2($id_os2, $columns = "*")
    {
        $find = $this->find("id_os2 = :id_os2", "id_os2={$id_os2}", $columns, false);
        return $find->fetch(true);
    }
}
