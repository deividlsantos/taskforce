<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Os2_2_1 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("os2_2_1", ["id_emp2", "id_os2", "id_os2_2", "id_chkitens"], "id");
    }
}
