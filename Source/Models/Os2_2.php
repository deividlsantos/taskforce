<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os2_2 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os2_2", ["id_emp2", "id_os2", "id_equipamentos"], "id");
    }

    public function findByOs2($id_os2, $columns = "*"){
        $find = $this->find("id_os2 = :id_os2", "id_os2={$id_os2}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }
}
