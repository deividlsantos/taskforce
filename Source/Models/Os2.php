<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os2 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os2", ["id_emp2", "id_os1"], "id");
    }

    public function findByIdOs($id_os, $columns = "*"){
        $find = $this->find("id_os1 = :id_os1", "id_os1={$id_os}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }

    public function findByOper($id_colaborador, $columns = "*"){
        $find = $this->find("id_colaborador = :id_colaborador", "id_colaborador={$id_colaborador}", $columns, false);
        return $find->order("id asc")->fetch(true);
    }
}
