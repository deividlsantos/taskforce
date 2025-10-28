<?php

namespace Source\Controllers;


use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp1;
use Source\Models\Emp2;
use Source\Models\Sidebar;
use Source\Models\Users;

class Login
{
    private $view;

    private $message;

    public function __construct($router)
    {
        $this->view = new Engine(CONF_APP_PATH . "Views");
        $this->view->addData(["router" => $router]);
        $this->message = new Message();
    }

    /**
     * SITE LOGIN
     * @param array|null $data
     * @return void
     */
    public function login(?array $data): void
    {
        // $emp = new Emp1();
        // $emp->descricao = "EMPRESA TESTE RIO PRETO AR";

        // if(!$emp->save){
        //     echo "ERRO!";
        // }else{
        //     echo "EMPRESA CADASTRADA";
        // }
        // exit;

        // $emp = new Emp2();
        // $emp->id_emp1 = 5;
        // $emp->razao = "EMPRESA TESTE RIO PRETO AR";
        // $emp->fantasia = "RIO PRETO AR";

        // if(!$emp->save){
        //     echo "ERRO!";
        // }else{
        //     echo "EMPRESA 2 CADASTRADA";
        // }
        // exit;

        // $user = new Users();
        // $user->id_emp2 = 5;
        // $user->nome = "ALEXANDRE";
        // $user->senha = "123456";
        // $user->nivel = 0;
        // $user->tipo = 1;
        // $user->email = "alex.tcsistemas@gmail.com";

        // if(!$user->save){
        //     echo "ERRO!";
        // }else{
        //     echo "USUÁRIO CADASTRADO";
        // }
        // exit;

        if (Auth::user()) {
            redirect("dash");
            exit;
        }

        if ("POST" == $_SERVER['REQUEST_METHOD']) {
            if (empty($data['email']) || empty($data['senha'])) {
                $json['message'] = $this->message->warning("Informe seu email e senha para entrar")->render();
                echo json_encode($json);
                return;
            }

            $save = (!empty($data['save']) ? true : false);
            $auth = new Auth();
            $login = $auth->login($data['email'], $data['senha'], $save);

            if ($login) {
                $user = Auth::user();
                switch ($user->tipo) {
                    case '2':
                        $json['redirect'] = url("oper_dash");
                        break;
                    default:
                        if ($user->financeiro == "X") {
                            $user->url = url("dash");
                            $json['redirect'] = url("dash");
                        } else if ($user->financeiro != "X" && $user->os == "X") {
                            $user->url = url("ordens");
                            $json['redirect'] = url("ordens");
                        } else if ($user->financeiro != "X" && $user->os != "X" && $user->ponto == "X") {
                            $user->url = url("ponto/folhas");
                            $json['redirect'] = url("ponto/folhas");
                        } else {
                            $user->url = url("dash");
                            $json['redirect'] = url("dash");
                        };
                        break;
                }
            } else {
                $json['message'] = $auth->message()->render();
            }

            echo json_encode($json);
            return;
        }

        $front = [
            "titulo" => "Taskforce - Login",
        ];

        echo $this->view->render("login", [
            "front" => $front,
            "cookie" => filter_input(INPUT_COOKIE, "authEmail")
        ]);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }

    public function logout()
    {
        (new Message())->info("Logout efetuado com sucesso!")->flash();

        Auth::logout();
        redirect("login");
    }
}
