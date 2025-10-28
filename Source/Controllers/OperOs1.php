<?php

namespace Source\Controllers;

use DateTime;
use League\Plates\Engine;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Equipamentos;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Pag;
use Source\Models\Rec;
use Source\Models\Servico;
use Source\Models\Status;
use Source\Models\Users;

class OperOs1 extends OperController
{
    public function __construct()
    {
        // $this->view = new Engine(CONF_APP_PATH . "Views/tcsistemas.os-oper/", "php");
        // $this->message = new Message();
        // $this->user = Auth::user();

        // if (!$this->user) {
        //     $this->message->error("Para acessar é preciso logar-se")->flash();
        //     redirect("");
        // }

        parent::__construct();
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $status = (new Status())->find(null, null, "*", false)->fetch(true);

        $ent = (new Ent())->find(
            "tipo = :tipo",
            "tipo=3"
        )->fetch(true);

        $opers = (new Users())->find(
            "tipo = :tipo",
            "tipo=2"
        )->fetch(true);

        $servico = (new Servico())->find()->fetch(true);

        $equipamentos = (new Equipamentos())->find()->fetch(true);
        $empresa = (new Ent())->findById($id_empresa);

        foreach ($ent as $item) {
            foreach ($opers as $oper) {
                if ($item->id == $oper->id_ent) {
                    $operador[] = $oper;
                }
            }
        }

        $os2 = (new Os2())->findByOper($id_func);

        if (!empty($os2)) {
            foreach ($os2 as $tarefas) {
                $ordemPai = (new Os1())->findById($tarefas->id_os1);
                $cliente = (new Ent())->findById($ordemPai->id_cli);
                $tarefa = (new Servico())->findById($tarefas->id_servico);

                $tarefas->cliente = $cliente->nome;
                $tarefas->servico = $tarefa->nome;
            }

            $os2_ids = array_map(function ($os) {
                return $os->id_os1;
            }, $os2);
        } else {
            $os2_ids = [];
        }

        $os1 = (new Os1())->find(
            "id IN (" . implode(',', $os2_ids) . ")",
            null,
            "*",
            false
        )->fetch(true);

        if (!empty($os1)) {
            foreach ($os1 as $ordens) {
                $cliente = (new Ent())->findById($ordens->id_cli);
                $status = (new Status())->findById($ordens->id_status);

                $ordens->cliente = $cliente->nome;
                $ordens->status = $status->descricao;
                $ordens->cor = $status->cor;
            }
        }

        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "nav" => "Ordens de Serviço",
            "navback" => "oper_dash",
            "navlink" => "oper_os1"
        ];

        echo $this->view->render("os1/os1", [
            "front" => $front,
            "os1" => $os1,
            "os2" => $os2,
            "operador" => $operador,
            "servico" => $servico,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
        ]);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
