<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Emp2 extends DataLayer
{
    public function __construct()
    {
        parent::__construct("emp2", ["id_emp1", "razao"], "id");
    }

    /**
     * @param string $id_emp1
     * @param string $razao
     * @param string $fantasia
     * @param string $cnpj
     * @param string $endereco
     * @param string $numero
     * @param string $bairro
     * @param string $cidade
     * @param string $uf
     * @param string $cep
     * @param string $fone1
     * @param string $fone2
     * @param string $plano
     * @param string $qtdeadm
     * @param string $qtdeoper
     * @return Emp2
     */
    public function bootstrap(
        string $id_emp1,
        string $razao,
        string $fantasia,
        string $cnpj,
        string $endereco,
        string $numero,
        string $bairro,
        string $cidade,
        string $uf,
        string $cep,
        string $fone1,
        string $fone2,
        string $plano,
        string $qtdeadm,
        string $qtdeoper
    ): Emp2 {
        $this->id_emp1 = $id_emp1;
        $this->razao = $razao;
        $this->fantasia = $fantasia;
        $this->cnpj = $cnpj;
        $this->endereco = $endereco;
        $this->numero = $numero;
        $this->bairro = $bairro;
        $this->cidade = $cidade;
        $this->uf = $uf;
        $this->cep = $cep;
        $this->fone1 = $fone1;
        $this->fone2 = $fone2;
        $this->plano = $plano;
        $this->qtdeadm = $qtdeadm;
        $this->qtdeoper = $qtdeoper;
        return $this;
    }
}
