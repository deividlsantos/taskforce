<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class EqpEstoque extends DataLayer
{
    public function __construct()
    {
        parent::__construct("equipamentos_estoque", ["id_emp2", "id_equipamento", "id_local"]);
    }
}
