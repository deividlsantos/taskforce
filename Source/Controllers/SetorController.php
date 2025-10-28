<?php

namespace Source\Controllers;

use Source\Models\Log;
use Source\Models\Setor;

class SetorController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->os != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $setor = "";
        $setor = (new Setor())->find()->order("descricao")->fetch(true);

        $front = [
            "titulo" => "Setores - Taskforce",
            "user" => $this->user,
            "secTit" => "Setores"
        ];

        echo $this->view->render("tcsistemas.os/setor/setorList", [
            "front" => $front,
            "setor" => $setor
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $setor = "";
        $secTit = "Cadastrar";

        if (isset($data['id_setor'])) {
            $id = ll_decode($data['id_setor']);
            $setor = (new Setor())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Setor"
        ];

        echo $this->view->render("tcsistemas.os/setor/setorCad", [
            "front" => $front,
            "setor" => $setor
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_setor = ll_decode($data['id_setor']);

        if (ll_intValida($id_setor)) {
            $setor = (new Setor())->findById($id_setor);
            $antes = clone $setor->data();
            $acao = "U";
        } else {
            $setor = new Setor();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $setor->id_emp2 = $id_empresa;
        $setor->descricao = $data['descricao'];
        $setor->id_users = $id_user;

        $depois = $setor->data();

        if (!$setor->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, "setor", $setor->id, $antes, $depois);

        if (ll_intValida($id_setor)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('setor');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_setor = ll_decode($data['id_setor']);

        if (ll_intValida($id_setor)) {
            $setor = (new Setor())->findById($id_setor);
            $antes = clone $setor->data();

            if (!$setor->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", "setor", $setor->id, $antes, null);

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
