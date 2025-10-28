<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Recorrencias extends DataLayer
{

    public function __construct()
    {
        parent::__construct("recorrencias", ["descricao", "padrao"]);
    }

}