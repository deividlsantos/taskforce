<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Equipamentos extends DataLayer
{
    public function __construct()
    {
        parent::__construct("equipamentos", ["id_emp2", "id_users"], "id");
    }

    public function statusList(){
        $find = $this->find(null, null, "DISTINCT status");
        return $find->fetch(true);
    }
    public function classeEquipamentoList(){
        $find = $this->find(null, null, "DISTINCT classe_equipamento");
        return $find->fetch(true);
    }
    public function classeOperacionalList(){
        $find = $this->find(null, null, "DISTINCT classe_operacional");
        return $find->fetch(true);
    }
    public function especieEquipamentoList(){
        $find = $this->find(null, null, "DISTINCT especie_equipamento");
        return $find->fetch(true);
    }
}
