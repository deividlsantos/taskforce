<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Materiais extends DataLayer
{
    public function __construct()
    {
        parent::__construct("materiais", ["id_emp2", "descricao", "unidade", "valor", "custo", "id_users"], "id");
    }
}
