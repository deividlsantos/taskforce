<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Obras extends DataLayer
{
    public function __construct()
    {
        parent::__construct("ent_obra", ["id_emp2", "id_ent_cli", "nome",], "id");
    }
}
