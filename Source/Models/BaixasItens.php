<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class BaixasItens extends DataLayer
{
    public function __construct()
    {
        parent::__construct("baixas_itens", ["id_emp2"]);
    }
}
