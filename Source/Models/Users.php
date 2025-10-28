<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;
use Exception;

class Users extends DataLayer
{

    public function __construct()
    {
        parent::__construct("users", ["id_emp2", "nome", "senha", "nivel", "email"], "id");
    }

    /**
     * @param string $id_emp2
     * @param string $nome
     * @param string $senha
     * @param string $nivel
     * @param string $tipo
     * @param string $email
     * @return Users
     */
    public function bootstrap(
        string $id_emp2,
        string $nome,
        string $senha,
        string $nivel,
        string $tipo,
        string $email,
        string $status
    ): Users {
        $this->id_emp2 = $id_emp2;
        $this->nome = $nome;
        $this->senha = $senha;
        $this->nivel = $nivel;
        $this->tipo = $tipo;
        $this->email = $email;
        $this->status = $status;
        return $this;
    }

    public function save(): bool
    {
        if (!$this->validateEmail() || !$this->validatePass() || !parent::save()) {
            return false;
        }
        return true;
    }

    protected function validateEmail(): bool
    {

        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->fail = new Exception("Informe um email válido");
            return false;
        }

        $userByEmail = null;
        if (!$this->id) {
            $userByEmail = $this->find("email = :email", "email={$this->email}", "*", false)->count();
        } else {
            $userByEmail = $this->find("email = :email AND id != :id", "email={$this->email}&id={$this->id}", "*", false)->count();
        }

        if ($userByEmail) {
            $this->fail = new Exception("O email informado já está em uso");
            return false;
        }
        return true;
    }

    protected function validatePass(): bool
    {
        if (empty($this->senha) || strlen($this->senha) < 5) {
            $this->fail = new Exception("Informe uma senha com pelo menos 5 caracteres");
            return false;
        }

        if (password_get_info($this->senha)["algo"]) {
            // if (strlen($this->senha) < 5) {
            //     $this->fail = new Exception("Informe uma senha com pelo menos 5 caracteres");
            //     return false;
            // }
            return true;
        }

        $this->senha = password_hash($this->senha, PASSWORD_DEFAULT);
        return true;
    }

    public function findByEmail(string $email, string $columns = "*"): ?Users
    {
        $find = $this->find("email = :email", "email={$email}", $columns, false);
        return $find->fetch();
    }

    public function findById2(int $id, string $columns = "*"): ?Users
    {
        $find = $this->find("id = :id", "id={$id}", $columns, false)->fetch();

        if (!$find) {
            return null;
        }

        // Buscar os dados da Sidebar com base no id_emp2 do usuário
        $sidebar = (new Sidebar())->find("id_emp2 = :id_emp2 AND id_users = :id_users", "id_emp2={$find->id_emp2}&id_users={$id}", "os, financeiro, cadastros, arquivos, ponto", false)->fetch();


        if ($sidebar) {
            // Incorporar os atributos da Sidebar diretamente no objeto Users
            foreach ($sidebar->data() as $key => $value) {
                $find->$key = $value;
            }
        }

        if ($find->financeiro == "X") {
            $find->url = url("dash");
        } else if ($find->financeiro != "X" && $find->os == "X") {
            $find->url = url("ordens");
        } else if ($find->financeiro != "X" && $find->os != "X" && $find->ponto == "X") {
            $find->url = url("ponto/folhas");
        }

        return $find;
    }
}
