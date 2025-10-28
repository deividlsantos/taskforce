<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class EqpLocal extends DataLayer
{
    public function __construct()
    {
        parent::__construct("equipamentos_local", ["id_emp2", "descricao", "status"]);
    }
}
