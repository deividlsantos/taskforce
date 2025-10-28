<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Ent extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ent", ["id_emp2", "tipo", "nome", "status"], "id");
    }
}
