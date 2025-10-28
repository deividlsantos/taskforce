<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Horas extends DataLayer
{
    public function __construct()
    {
        parent::__construct("horas", ["id_emp2", "id_turno", "dia_semana", "hora_ini", "hora_fim"]);
    }
}
