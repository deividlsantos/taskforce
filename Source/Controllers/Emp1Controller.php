<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp1;

class Emp1Controller extends Controller
{
    public function __construct()
    {
        $this->view = new Engine(CONF_APP_PATH . "Views/", "php");
        $this->message = new Message();
        $this->user = Auth::user();

        if ($this->user->tipo < 5) {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $grupos = (new Emp1())->find(null, null, "*", false)->fetch(true);

        $front = [
            "titulo" => "Grupos - Task Force",
            "user" => $this->user,
            "secTit" => "Grupos (Emp1)"
        ];

        echo $this->view->render("tcsistemas.adm/grupo/grupoCad", [
            "front" => $front,
            "grupos" => $grupos
        ]);
    }

    public function form(array $data): void
    {
        $grupo = null;
        $grupos = (new Emp1())->find()->fetch(true);

        if (isset($data["id_empgroup"])) {
            $grupo = (new Emp1())->findById($data["id_empgroup"]);
            if (!$grupo) {
                $this->message->warning("Grupo não encontrado")->flash();
                redirect("empgroup");
            }
        }

        $front = [
            "titulo" => "Busca de empresa por Grupo - Task Force",
            "user" => $this->user,
            "secTit" => "Busca de empresa por Grupo"
        ];

        echo $this->view->render("tcsistemas.adm/grupo/grupoCad", [
            "front" => $front,
            "grupo" => $grupo, // Alterado de "empresa" para "grupo"

        ]);
    }

    public function salvar(array $data): void
    {
        $id = ll_decode($data['id_empgroup']) ?? null;
        $descricao = $data['descricao'] ?? null;

        if (empty($descricao)) {
            $json['message'] = $this->message->warning("Informe o nome do Grupo.")->render();
            echo json_encode($json);
            return;
        }

        // Carrega o grupo existente ou cria um novo
        $grupo = $id ? (new Emp1())->findById($id) : new Emp1();
        $grupo->descricao = $descricao;

        if (!$grupo->save()) {
            $json['message'] = $this->message->error("Erro ao salvar o cadastro")->render();
            echo json_encode($json);
            return;
        }

        if (ll_intValida($id)) {
            $json['message'] = $this->message->success("Grupo atualizado com sucesso")->render();
        } else {
            $json['message'] = $this->message->success("Grupo cadastrado com sucesso")->flash();
            $json['reload'] = true;
        }

        echo json_encode($json);
    }

    public function excluir(array $data): void
    {
        $id = $data['id'] ?? null;

        $grupo = (new Emp1())->findById($id);

        if ($id < 6) {
            $json['message'] = $this->message->error("Grupo padrão não pode ser excluído")->render();
            echo json_encode($json);
            return;
        }

        if ($grupo->destroy()) {
            $json['message'] = $this->message->success("Grupo excluído com sucesso")->flash();
        } else {
            $json['message'] = $this->message->error("Erro ao excluir o grupo")->flash();
        }
        $json['reload'] = true;

        echo json_encode($json);
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
