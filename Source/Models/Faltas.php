<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Faltas extends DataLayer
{

    public function __construct()
    {
        parent::__construct("faltas", ["id_emp2"]);
    }

}