<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Plconta extends DataLayer
{
    public function __construct()
    {
        parent::__construct("plconta", ["id_emp2"], "id");
    }
}
