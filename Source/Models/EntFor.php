<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class EntFor extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ent_for", ["id_emp2", "id_ent"], "id");
    }

    public function findByIdEnt($id_ent, $columns = "*"){
        $find = $this->find("id_ent = :id_ent", "id_ent={$id_ent}", $columns, false);
        return $find->fetch();
    }
}