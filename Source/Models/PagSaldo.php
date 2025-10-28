<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class PagSaldo extends DataLayer
{
    public function __construct()
    {
        parent::__construct("pagsaldo", ["id_emp2"], "id");
    }

    public function findByIdPag($id_pag, $columns = "*"){
        $find = $this->find("id_pag = :id_pag", "id_pag={$id_pag}", $columns, false);
        return $find->fetch(true);
    }
}
