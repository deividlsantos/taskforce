<?php

namespace Source\Controllers;

use Source\Models\Faltas;
use Source\Models\Log;

class FaltasController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->ponto != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function faltas(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $front = [
            "titulo" => "Observações do Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => "Observações"
        ];

        echo $this->view->render("tcsistemas.ponto/faltas/faltas", [
            "front" => $front
        ]);
    }

    public function lista(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $faltas = new Faltas();

        $dadosFaltas = $faltas->find()->fetch(true);

        $front = [
            "titulo" => "Observações do Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => "Lista de Observações"
        ];

        echo $this->view->render("tcsistemas.ponto/faltas/faltasList", [
            "front" => $front,
            "faltas" => $dadosFaltas
        ]);
    }

    public function faltasForm(?array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $faltas = "";

        $secTit = "Cadastrar Observação";

        if (isset($data['id_faltas'])) {
            $id_faltas = ll_decode($data['id_faltas']);
            if (ll_intValida($id_faltas)) {
                $faltas = (new Faltas())->findById($id_faltas);
            }
            $secTit = "Editar Cadastro Observação";
        }

        $front = [
            "titulo" => "Observações do Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit,
        ];

        echo $this->view->render("tcsistemas.ponto/faltas/faltasCad", [
            "faltas" => $faltas,
            "front" => $front
        ]);
    }



    public function salvar(array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            if (!filter_var($data['descricao'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            $id_faltas = ll_decode($data['id_faltas']);

            if (ll_intValida($id_faltas)) {
                $faltas = (new Faltas())->findById($id_faltas);
                $antes = $faltas->data();
                $acao = "U";
            } else {
                $faltas = new Faltas();
                $antes = null;
                $acao = "C";
            }

            $faltas->id_emp2 = $id_empresa;
            $faltas->descricao = $data['descricao'];
            $faltas->id_users = $id_user;


            if (!$faltas->save) {
                $json['message'] = $this->message->error("Erro ao cadastrar, por favor verifique os dados!")->render();
                echo json_encode($json);
                return;
            }

            $depois = $faltas->data();

            $log = new Log();
            $log->registrarLog($acao, $faltas->getEntity(), $faltas->id, $antes, $depois);
        }

        if (ll_intValida($id_faltas)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json["redirect"] = url("faltas");
        echo json_encode($json);
    }

    public function apagar($data): void
    {
        $id_faltas = ll_decode($data['id_faltas']);

        if (ll_intValida($id_faltas)) {
            $faltas = (new Faltas())->findById($id_faltas);
            $antes = clone $faltas->data();

            if ($faltas->destroy()) {
                $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
                $json["redirect"] = url("faltas");
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $faltas->getEntity(), $faltas->id, $antes, null);
        }
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
