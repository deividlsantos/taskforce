<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Rec extends DataLayer
{
    public function __construct()
    {
        parent::__construct("rec", ["id_emp2"], "id");
    }
}
