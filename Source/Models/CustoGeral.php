<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class CustoGeral extends DataLayer
{
    public function __construct()
    {
        parent::__construct("custo_geral", ["id_emp2"], "id");
    }
}
