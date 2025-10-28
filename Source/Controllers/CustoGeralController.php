<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\CustoGeral;
use Source\Models\Log;

class CustoGeralController
{

    private $view;
    private $message;
    protected $user;

    public function __construct()
    {
        $this->view = new Engine(CONF_APP_PATH . "Views/", "php");
        $this->message = new Message();
        $this->user = Auth::user();

        if (!$this->user) {
            $this->message->error("Para acessar é preciso logar-se")->flash();
            redirect("");
        }

        if ($this->user->id_emp2 != 1 && $this->user->financeiro != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $custogeral = "";
        $custogeral = (new CustoGeral())->find()->fetch(true);

        $front = [
            "titulo" => "Custos Gerais - Taskforce",
            "user" => $this->user,
            "secTit" => "Custos Gerais"
        ];

        echo $this->view->render("tcsistemas.os/custogeral/custogeralList", [
            "front" => $front,
            "custogeral" => $custogeral
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $custogeral = "";
        $secTit = "Cadastrar";

        if (isset($data['id_custogeral'])) {
            $id = ll_decode($data['id_custogeral']);
            $custogeral = (new CustoGeral())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Custo Geral"
        ];

        echo $this->view->render("tcsistemas.os/custogeral/custogeralCad", [
            "front" => $front,
            "custogeral" => $custogeral
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_custogeral = ll_decode($data['id_custogeral']);

        if (ll_intValida($id_custogeral)) {
            $custogeral = (new CustoGeral())->findById($id_custogeral);
            $antes = clone $custogeral->data();
            $acao = "U";
        } else {
            $custogeral = new CustoGeral();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (!float_verify($data['percentual'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'PERCENTUAL'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $custogeral->id_emp2 = $id_empresa;
        $custogeral->descricao = $data['descricao'];
        $custogeral->percentual = moedaSql($data['percentual']);
        $custogeral->id_users = $id_user;

        $depois = $custogeral->data();

        if (!$custogeral->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, $custogeral->getEntity(), $custogeral->id, $antes, $depois);

        if (ll_intValida($id_custogeral)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('custogeral');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_custogeral = ll_decode($data['id_custogeral']);

        if (ll_intValida($id_custogeral)) {
            $custogeral = (new CustoGeral())->findById($id_custogeral);

            $antes = clone $custogeral->data();

            if (!$custogeral->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $custogeral->getEntity(), $custogeral->id, $antes, null);

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
