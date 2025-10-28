<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class RecSaldo extends DataLayer
{
    public function __construct()
    {
        parent::__construct("recsaldo", ["id_emp2"], "id");
    }

    public function findByIdRec($id_rec, $columns = "*"){
        $find = $this->find("id_rec = :id_rec", "id_rec={$id_rec}", $columns, false);
        return $find->fetch(true);
    }
}
