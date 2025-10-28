<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Tipo extends DataLayer
{

    public function __construct()
    {
        parent::__construct("os_tipo", ["id_emp2"]);
    }

}