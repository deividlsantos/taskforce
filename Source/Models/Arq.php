<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Arq extends DataLayer
{

    public function __construct()
    {
        parent::__construct("arq", ["id_emp2", "tipo", "arquivo", "descricao", "nome_arquivo", "extensao", "chave"]);
    }

}