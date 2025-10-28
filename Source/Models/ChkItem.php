<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class ChkItem extends DataLayer
{
    public function __construct()
    {
        parent::__construct("chkitens", ["id_emp2", "id_chkgrupo", "descricao"]);
    }
}
