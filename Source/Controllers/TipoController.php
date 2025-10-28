<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Log;
use Source\Models\Obs;
use Source\Models\Tipo;

class TipoController extends Controller
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

        $tipo = "";
        $tipo = (new Tipo())->find()->order("id")->fetch(true);

        if (!empty($tipo)) {
            foreach ($tipo as $key => $t) {
                $t->padrao = 'N';
                if ($key == 0) {
                    $t->padrao = 'S';
                }
            }
        }

        $front = [
            "titulo" => "Tipos de OS - Taskforce",
            "user" => $this->user,
            "secTit" => "Tipos de OS"
        ];

        echo $this->view->render("tcsistemas.os/tipo/tipoList", [
            "front" => $front,
            "tipo" => $tipo
        ]);
    }

    public function form($data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $tipo = "";
        $secTit = "Cadastrar";

        if (isset($data['id_tipo'])) {
            $id = ll_decode($data['id_tipo']);
            $tipo = (new Tipo())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $front = [
            "titulo" => "Tipos de OS - Taskforce",
            "user" => $this->user,
            "secTit" => $secTit . " Tipos de OS"
        ];

        echo $this->view->render("tcsistemas.os/tipo/tipoCad", [
            "front" => $front,
            "tipo" => $tipo
        ]);
    }

    public function salvar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_tipo = ll_decode($data['id_tipo']);

        if (ll_intValida($id_tipo)) {
            $tipo = (new Tipo())->findById($id_tipo);
            $antes = clone $tipo->data();
            $acao = "U";
        } else {
            $tipo = new Tipo();
            $antes = null;
            $acao = "C";
        }

        if (empty($data['descricao'])) {
            $json['message'] = $this->message->error("O campo 'DESCRIÇÃO' é obrigatório!")->render();
            echo json_encode($json);
            return;
        }

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        $tipo->id_emp2 = $id_empresa;
        $tipo->descricao = $data['descricao'];
        $tipo->id_users = $id_user;

        $depois = $tipo->data();

        if (!$tipo->save) {
            $json['message'] = $this->message->warning("Erro ao salvar!")->render();
            echo json_encode($json);
            return;
        }

        $log = new Log();
        $log->registrarLog($acao, "os_tipo", $tipo->id, $antes, $depois);

        if (ll_intValida($id_tipo)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json['redirect'] = url('tipo');
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_tipo = ll_decode($data['id_tipo']);

        $tipos = (new Tipo())->find()->order('id')->fetch(true);

        if ($tipos[0]->id == $id_tipo) {
            $json['message'] = $this->message->error("REGISTRO PADRÃO. NÃO É POSSÍVEL EXCLUIR!")->render();
            echo json_encode($json);
            return;
        }

        if (ll_intValida($id_tipo)) {
            $tipo = (new Tipo())->findById($id_tipo);
            $antes = clone $tipo->data();

            if (!$tipo->destroy()) {
                if ($tipo->fail()) {
                    $e = $tipo->fail();

                    if ($e instanceof \PDOException && $e->getCode() == "23000" && strpos($e->getMessage(), "1451") !== false) {
                        $json['message'] = $this->message->error("Não é possível excluir este tipo porque ele já está sendo utilizado em ordens de serviço.")->render();
                    } else {
                        $json['message'] = $this->message->error("Erro ao tentar excluir!")->render();
                    }

                    echo json_encode($json);
                    return;
                }
            }

            $log = new Log();
            $log->registrarLog("D", "os_tipo", $tipo->id, $antes, null);

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
