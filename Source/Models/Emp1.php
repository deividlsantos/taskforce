<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Emp1 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("emp1", ["descricao"], "id");
    }
}
