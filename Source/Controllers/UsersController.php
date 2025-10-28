<?php

namespace Source\Controllers;

use Source\Models\Ent;
use Source\Models\Horas;
use Source\Models\Sidebar;
use Source\Models\Turno;
use Source\Models\Users;

class UsersController extends Controller
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
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        $users = new Users();

        $dadosUsers = $users->find(
            "id_emp2 = :id_emp2",
            "id_emp2={$id_empresa}"
        )->fetch(true);

        $front = [
            "titulo" => "Usuários - Taskforce",
            "user" => $this->user,
            "secTit" => "Usuários"
        ];

        echo $this->view->render("tcsistemas.financeiro/users/usersList", [
            "users" => $dadosUsers,
            "front" => $front
        ]);
    }

    public function form(?array $data): void
    {
        $id_empresa = $this->user->id_emp2;

        $users = "";
        $secTit = "Cadastrar Usuário";
        if (isset($data['id_user'])) {
            $id_user = ll_decode($data['id_user']);
            if (ll_intValida($id_user)) {
                $users = (new Users())->findById($id_user);
                $permissoes = (new Sidebar())->find("id_emp2 = :id_emp2 AND id_users = :id_users", "id_emp2={$id_empresa}&id_users={$id_user}")->fetch();

                if (!empty($permissoes)) {
                    $users->os = $permissoes->os;
                    $users->financeiro = $permissoes->financeiro;
                    $users->cadastros = $permissoes->cadastros;
                    $users->ponto = $permissoes->ponto;
                    $users->arquivos = $permissoes->arquivos;
                } else {
                    $users->os = '';
                    $users->financeiro = '';
                    $users->cadastros = 'X';
                    $users->ponto = '';
                    $users->arquivos = 'X';
                }
            }
            $secTit = "Visualizar/Editar Usuário";
        }

        $colaboradores = (new Ent())->find(
            "id_emp2 = :id_emp2 AND tipo = :tipo AND status = :status",
            "id_emp2={$id_empresa}&tipo=3&status=A"
        )->fetch(true);

        $front = [
            "titulo" => "USUÁRIOS - TC SISTEMAS",
            "user" => $this->user,
            "secTit" => $secTit
        ];

        echo $this->view->render("tcsistemas.financeiro/users/usersCad", [
            "users" => $users,
            "front" => $front,
            "colaboradores" => $colaboradores,
            "user" => $this->user
        ]);
    }



    public function salvar(array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            if (!is_email($data['email'])) {
                $json['message'] = $this->message->error("Digite um email válido!")->render();
                echo json_encode($json);
                return;
            }

            if (!str_verify($data['nome'])) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NOME'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (empty($data['nome'])) {
                $json['message'] = $this->message->error("Digite o nome!")->render();
                echo json_encode($json);
                return;
            }

            $id_users = ll_decode($data['id_users']);

            if (ll_intValida($id_users)) {
                $user = (new Users())->findById($id_users);
            } else {
                $user = new Users();

                $usuarios = (new Users())->find(
                    "id_emp2 = :id_emp2",
                    "id_emp2={$id_empresa}"
                )->fetch(true);

                if ($data['tipo'] == 2 && $data['ent'] == 0) {
                    $json['message'] = $this->message->error("Selecione um colaborador para o operador!")->render();
                    echo json_encode($json);
                    return;
                }

                if (!empty($usuarios)) {
                    foreach ($usuarios as $usuario) {
                        if ($usuario->id_ent == $data['ent']) {
                            $json['message'] = $this->message->error("Já existe usuário cadastrado para esse colaborador.")->render();
                            echo json_encode($json);
                            return;
                        }
                    }
                }
            }

            if (!empty($data["senha"])) {
                if (empty($data["senha_re"]) || $data["senha"] != $data["senha_re"]) {
                    $json["message"] = $this->message->warning("Para alterar sua senha, informe e repita a nova senha!")->render();
                    echo json_encode($json);
                    return;
                }

                $user->senha = $data["senha"];
            }

            $ent = isset($data['ent']) && $data['ent'] > 0 ? $data['ent'] : null;

            $user->id_emp2 = $id_empresa;
            $user->id_ent = $ent;
            $user->email = $data['email'];
            $user->nome = $data['nome'];
            $user->tipo = $data['tipo'];
            $user->nivel = $data['tipo'] == 5 ? 5 : 1;
            $user->status = 'C';

            $menuOs = '';
            $menuFinanceiro = '';
            $menuCadastros = '';
            $menuPonto = '';
            $menuArquivos = '';

            if (isset($data['os_sidebar']) && $data['os_sidebar'] == 'on') {
                $menuOs = 'X';
            }
            if (isset($data['financeiro_sidebar']) && $data['financeiro_sidebar'] == 'on') {
                $menuFinanceiro = 'X';
            }
            if (isset($data['cadgeral_sidebar']) && $data['cadgeral_sidebar'] == 'on') {
                $menuCadastros = 'X';
            }
            if (isset($data['ponto_sidebar']) && $data['ponto_sidebar'] == 'on') {
                $menuPonto = 'X';
            }
            if (isset($data['arquivos_sidebar']) && $data['arquivos_sidebar'] == 'on') {
                $menuArquivos = 'X';
            }

            if (!$user->save) {
                $json['message'] = $this->message->warning($user->fail()->getMessage())->render();
                echo json_encode($json);
                return;
            } else {
                if (ll_intValida($id_users)) {
                    $permissoes = (new Sidebar())->find("id_emp2 = :id_emp2 AND id_users = :id_users", "id_emp2={$id_empresa}&id_users={$id_users}")->fetch();

                    if (empty($permissoes)) {
                        $permissoes = new Sidebar();
                        $permissoes->id_emp2 = $id_empresa;
                        $permissoes->id_users = $id_users;

                        $permissoes->os = $menuOs;
                        $permissoes->financeiro = $menuFinanceiro;
                        $permissoes->cadastros = $menuCadastros;
                        $permissoes->ponto = $menuPonto;
                        $permissoes->arquivos = $menuArquivos;
                        $permissoes->save;
                    } else {
                        $permissoes->os = $menuOs;
                        $permissoes->financeiro = $menuFinanceiro;
                        $permissoes->cadastros = $menuCadastros;
                        $permissoes->ponto = $menuPonto;
                        $permissoes->arquivos = $menuArquivos;

                        $permissoes->save;
                    }
                    $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
                } else {
                    $permissoes = new Sidebar();
                    $permissoes->id_emp2 = $id_empresa;
                    $permissoes->id_users = $user->id;
                    $permissoes->os = $menuOs;
                    $permissoes->financeiro = $menuFinanceiro;
                    $permissoes->cadastros = $menuCadastros;
                    $permissoes->ponto = $menuPonto;
                    $permissoes->arquivos = $menuArquivos;

                    $permissoes->save;
                    $this->message->success("CADASTRADO COM SUCESSO!")->flash();
                }
            }
            $json["redirect"] = url("users");
            echo json_encode($json);
        }
    }

    public function excluir($data): void
    {
        $id = ll_decode($data['id_user']);

        if (ll_intValida($id)) {
            $user = (new Users())->findById($id);
            $sidebar = (new Sidebar())->find("id_users = :id_users", "id_users={$id}")->fetch();

            $sidebar->destroy();

            if ($user) {
                if ($user->destroy()) {
                    $this->message->success("Usuário excluído com sucesso!")->flash();
                } else {
                    $this->message->error("Erro ao excluir usuário: {$user->fail()->getMessage()}")->flash();
                }
            } else {
                $this->message->error("Usuário não encontrado!")->flash();
            }
        }

        $json['reload'] = true;
        echo json_encode($json);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
