<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Obs extends DataLayer
{

    public function __construct()
    {
        parent::__construct("obs", ["id_emp2"]);
    }

}