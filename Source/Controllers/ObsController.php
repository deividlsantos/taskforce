<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\Obs;

class ObsController extends Controller
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

        $obs = "";
        $obs = (new Obs())->find()->order("descricao")->fetch(true);

        $front = [
            "titulo" => "Observações - Taskforce",
            "user" => $this->user,
            "secTit" => "Observações"
        ];

        echo $this->view->render("tcsistemas.os/obs/obsList", [
            "front" => $front,
            "obs" => $obs
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $obs = "";
        $secTit = "Cadastrar";

        if (isset($data['id_obs'])) {
            $id = ll_decode($data['id_obs']);
            $obs = (new Obs())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Cadastros - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Observações"
        ];

        echo $this->view->render("tcsistemas.os/obs/obsCad", [
            "front" => $front,
            "obs" => $obs
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_obs = ll_decode($data['id_obs']);

        if (ll_intValida($id_obs)) {
            $obs = (new Obs())->findById($id_obs);
            $antes = clone $obs->data();
            $acao = "U";
        } else {
            $obs = new Obs();
            $antes = null;
            $acao = "C";
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $obs->id_emp2 = $id_empresa;
        $obs->descricao = $data['descricao'];
        $obs->id_users = $id_user;

        $depois = $obs->data();

        if (!$obs->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, "obs", $obs->id, $antes, $depois);

        if (ll_intValida($id_obs)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('obs');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_obs = ll_decode($data['id_obs']);

        if (ll_intValida($id_obs)) {
            $obs = (new Obs())->findById($id_obs);
            $antes = clone $obs->data();

            if (!$obs->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", "obs", $obs->id, $antes, null);

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
