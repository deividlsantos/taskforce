<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class RecorrenciasCliServ extends DataLayer
{

    public function __construct()
    {
        parent::__construct("recorrencias_cli_serv", ["id_emp2", "id_cli", "id_servico", "id_recorrencia", "id_users"]);
    }

    public function findRecorrencia($id_cli, $id_servico)
    {
        return $this->find("id_cli = :cli AND id_servico = :servico", "cli={$id_cli}&servico={$id_servico}")->fetch();
    }
}