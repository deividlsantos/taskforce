<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\ChkGrupo;
use Source\Models\ChkItem;
use Source\Models\Log;
use Source\Models\Materiais;

class ChecklistController extends Controller
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

        $front = [
            "titulo" => "Checklist - Taskforce",
            "user" => $this->user,
            "secTit" => "Checklist"
        ];

        echo $this->view->render("tcsistemas.os/checklist/checklist-index", [
            "front" => $front
        ]);
    }

    public function grupos(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $grupos = (new ChkGrupo())->find()->fetch(true);

        $front = [
            "titulo" => "Grupos - Taskforce",
            "user" => $this->user,
            "secTit" => "Grupos de Checklist"
        ];

        echo $this->view->render("tcsistemas.os/checklist/checklist-grupos", [
            "front" => $front,
            "grupos" => $grupos
        ]);
    }

    public function salvargrupo($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $ChkGrupo_id = isset($data['id']) ? ll_decode($data['id']) : null;

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o 'NOVO GRUPO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (ll_intValida($ChkGrupo_id)) {
            $grupo = (new ChkGrupo())->findById($ChkGrupo_id);
            $antes = clone $grupo->data();
            $acao = "U";
        } else {
            $grupo = new ChkGrupo();
            $antes = null;
            $acao = "C";
        }

        $grupo->id_emp2 = $id_empresa;
        $grupo->descricao = $data['descricao'];
        $grupo->id_users = $id_user;

        $log = new Log();
        $log->registrarLog($acao, $grupo->getEntity(), $grupo->id, $antes, $grupo->data());

        if (!$grupo->save) {
            $json['message'] = $this->message->error("ERRO AO TENTAR SALVAR O GRUPO!")->render();
            echo json_encode($json);
            return;
        }

        $grupos = (new ChkGrupo())->find()->fetch(true);
        $grupoData = [];
        if (!empty($grupos)) {
            foreach ($grupos as $g) {
                $g->id_encode = ll_encode($g->id);
            }
            $grupoData = objectsToArray($grupos);
        }

        if (ll_intValida($ChkGrupo_id)) {
            $json['mensagem'] = $this->message->success("REGISTRO ALTERADO COM SUCESSO")->render();
        } else {
            $json['mensagem'] = $this->message->success("CADASTRADO COM SUCESSO!")->render();
            $json['grupos'] = $grupoData;
        }

        $json['checklist'] = true;

        echo json_encode($json);
    }

    public function excluirgrupo($data): void
    {
        $id = ll_decode($data['id']);

        if (ll_intValida($id)) {
            $grupo = (new ChkGrupo())->findById($id);

            $antes = clone $grupo->data();

            if (!$grupo->destroy()) {
                $json['message'] = $this->message->error("ERRO AO TENTAR EXCLUIR!")->render();
                echo json_encode($json);
            }

            $log = new Log();
            $log->registrarLog("D", $grupo->getEntity(), $grupo->id, $antes, null);

            $json['message'] = $this->message->success("REGISTRO EXCLUÍDO COM SUCESSO")->render();
            $json['status'] = "success";
            echo json_encode($json);
        }
    }

    public function itens(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $grupos = (new ChkGrupo())->find()->fetch(true);
        $itens = (new ChkItem())->find()->fetch(true);

        if (!empty($itens)) {
            foreach ($itens as $item) {
                $item->grupo = (new ChkGrupo())->findById($item->id_chkgrupo)->descricao;
            }
        }

        $front = [
            "titulo" => "Itens - Taskforce",
            "user" => $this->user,
            "secTit" => "Itens de Checklist"
        ];

        echo $this->view->render("tcsistemas.os/checklist/checklist-itens", [
            "front" => $front,
            "grupos" => $grupos,
            "itens" => $itens
        ]);
    }

    public function salvaritem($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $chkitem_id = isset($data['id']) ? ll_decode($data['id']) : null;

        if (!str_verify($data['descricao'])) {
            $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'DESCRIÇÃO'. Tente novamente!")->render();
            echo json_encode($json);
            return;
        }

        if (ll_intValida($chkitem_id)) {
            $chkitem = (new ChkItem())->findById($chkitem_id);
            $antes = clone $chkitem->data();
            $acao = "U";
        } else {
            $chkitem = new ChkItem();
            $antes = null;
            $acao = "C";
        }

        $chkitem->id_emp2 = $id_empresa;
        $chkitem->id_chkgrupo = $data['id_chkgrupo'];
        $chkitem->descricao = $data['descricao'];
        $chkitem->id_users = $id_user;

        $log = new Log();
        $log->registrarLog($acao, $chkitem->getEntity(), $chkitem->id, $antes, $chkitem->data());

        if (!$chkitem->save) {
            $chkitem->fail();
            // var_dump($chkitem->fail()->getMessage());
            // exit;
            $json['message'] = $this->message->error("ERRO AO TENTAR SALVAR O ITEM!")->render();
            echo json_encode($json);
            return;
        }

        if (ll_intValida($chkitem_id)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
            $json['reload'] = true;
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
            $json['reload'] = true;
        }

        echo json_encode($json);
    }

    public function retornaItem($data)
    {
        $id = ll_decode($data['id']);

        if (ll_intValida($id)) {
            $item = (new ChkItem())->findById($id);

            $itemData = [];
            if ($item) {
                $itemData = objectsToArray($item);
                $json['item'] = $itemData;
                $json['status'] = "success";
            } else {
                $json['message'] = $this->message->error("ITEM NÃO ENCONTRADO!")->render();
                $json['status'] = "error";
            }
        } else {
            $json['message'] = $this->message->error("ID INVÁLIDO!")->render();
            $json['status'] = "error";
        }

        echo json_encode($json);
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
