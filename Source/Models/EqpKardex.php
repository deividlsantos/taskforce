<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class EqpKardex extends DataLayer
{
    public function __construct()
    {
        parent::__construct("equipamentos_kardex", ["id_emp2", "id_equipamento", "id_local"]);
    }
}
