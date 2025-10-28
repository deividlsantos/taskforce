<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Feriados extends DataLayer
{

    public function __construct()
    {
        parent::__construct("feriados", ["id_emp2", "dias"]);
    }
}
