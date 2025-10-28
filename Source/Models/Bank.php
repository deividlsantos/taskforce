<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Bank extends DataLayer
{
    public function __construct()
    {
        parent::__construct("bank", [], "id");
    }
}
