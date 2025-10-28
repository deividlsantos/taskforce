<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Servico extends DataLayer
{
    public function __construct()
    {
        parent::__construct("servico", ["id_emp2", "nome", "id_users"], "id");
    }
}
