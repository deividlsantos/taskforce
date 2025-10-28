<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Oper extends DataLayer
{
    public function __construct()
    {
        parent::__construct("oper", ["id_emp2"], "id");
    }
}
