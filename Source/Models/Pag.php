<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Pag extends DataLayer
{
    public function __construct()
    {
        parent::__construct("pag", ["id_emp2"], "id");
    }
}
