<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Turno extends DataLayer
{
    public function __construct()
    {
        parent::__construct("turno", ["id_emp2"], "id");
    }
}
