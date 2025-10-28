<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Baixas extends DataLayer
{
    public function __construct()
    {
        parent::__construct("baixas", ["id_emp2"]);
    }
}
