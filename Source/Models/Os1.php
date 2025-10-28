<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os1 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os1", ["id_emp2"], "id");
    }
}
