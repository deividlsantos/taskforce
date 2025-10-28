<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\Status;

class StatusController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->user->tipo < 5) {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $status = "";
        $status = (new Status())->find(
            "id_emp2 = :id_emp2",
            "id_emp2=1",
            "id, descricao, cor",
            false
        )->fetch(true);

        $front = [
            "titulo" => "Status - Taskforce",
            "user" => $this->user,
            "secTit" => "Status"
        ];

        echo $this->view->render("tcsistemas.os/status/statusList", [
            "front" => $front,
            "status" => $status
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $status = "";
        $secTit = "Cadastrar";

        if (isset($data['id_status'])) {
            $id = ll_decode($data['id_status']);
            $status = (new Status())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Status"
        ];

        echo $this->view->render("tcsistemas.os/status/statusCad", [
            "front" => $front,
            "status" => $status
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_status = ll_decode($data['id_status']);

        if (ll_intValida($id_status)) {
            $status = (new Status())->findById($id_status);
            $antes = clone $status->data();
            $acao = "U";
        } else {
            $status = new Status();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['cor'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $status->id_emp2 = $id_empresa;
        $status->descricao = $data['descricao'];
        $status->cor = $data['cor'];
        $status->id_users = $id_user;

        if (!$status->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $status->getEntity(), $status->id, $antes, $status->data());

        if (ll_intValida($id_status)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('status');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_status = ll_decode($data['id_status']);

        if (ll_intValida($id_status)) {
            $status = (new Status())->findById($id_status);
            $antes = clone $status->data();

            if (!$status->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $status->getEntity(), $status->id, $antes, null);

            $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
            $json["reload"] = true;
            echo json_encode($json);
        }
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
