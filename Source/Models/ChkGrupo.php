<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class ChkGrupo extends DataLayer
{
    public function __construct()
    {
        parent::__construct("chkgrupo", ["id_emp2", "descricao"]);
    }
}
