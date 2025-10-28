<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp2;

class OperController
{

    protected $view;
    protected $message;
    protected $user;

    public function __construct()
    {
        $this->view = new Engine(CONF_APP_PATH . "Views/tcsistemas.os-oper", "php");
        $this->message = new Message();
        $this->user = Auth::user();

        if (!$this->user) {
            $this->message->error("Para acessar é preciso logar-se")->flash();
            redirect("");
        }

        $emp2 = (new Emp2())->findById($this->user->id_emp2);
        $consulta = verificaPermissao(cnpj_cript($emp2->cnpj, 'C'));

        if (verificaExpiracao($consulta) == '3') {
            $this->renderLicencaExpirada();
        }
    }

    protected function renderLicencaExpirada()
    {
        Auth::logout();
        echo $this->view->render("licenca_expirada", [
            "data" => (object)[
                "title" => "Atenção",
                "desc" => "Entre em contato com a TaskForce!",
                "link" => true,
                "linkTitle" => "ENTRAR EM CONTATO"
            ]
        ]);
        exit; // evita que o script continue e outro controller seja carregado
    }
}
