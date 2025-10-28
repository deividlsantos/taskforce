<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class EqpMov extends DataLayer
{
    public function __construct()
    {
        parent::__construct("equipamentos_mov", ["id_emp2", "id_equipamento", "qtde"]);
    }
}
