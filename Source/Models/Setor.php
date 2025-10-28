<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Setor extends DataLayer
{
    public function __construct()
    {
        parent::__construct("setor", ["id_emp2"], "id");
    }
}
