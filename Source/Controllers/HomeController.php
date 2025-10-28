<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Users;

class HomeController
{

    private $view;
    private $message;
    protected $user;

    public function __construct()
    {
        $this->view = new Engine(CONF_APP_PATH . "Views", "php");
        $this->message = new Message();
        $this->user = Auth::user();
    }

    public function index(): void
    {

        $menu = '<ul class="sub-menu">
                    <li>
                        <a href="#fun">Funcionalidades</a>
                    </li>
                    <li>
                        <a href="#seg">Segmentos</a>
                    </li>
                    <li>
                        <a href="#val">Preço</a>
                    </li>
                    <li>
                        <a href="#con">Contato</a>
                    </li>
                </ul>';

        $btnLogin = '<div class="donate-btn-header">
													<a class="dtbtn" href="' . url('login') . '">Entrar</a>
												</div>';
        $btnRegister = '<div class="donate-btn-header">
													<a class="dtbtn" href="' . url('cadastro') . '">Cadastrar</a>
												</div>';

        $front = [
            "titulo" => "Taskforce",
            "user" => $this->user,
            "hmenu" => $menu,
            "login" => $btnLogin,
            "register" => $btnRegister
        ];

        echo $this->view->render("tcsistemas.home/taskforce/landing", [
            "front" => $front
        ]);
    }

    /**
     * @param array|null $data
     * @return void
     */
    public function register(?array $data): void
    {


        if ($_SERVER['REQUEST_METHOD'] == "POST") {

            // if (!empty($data['csrf'])) {
            // if (!csrf_verify($data)) {
            //     $this->message->error("Erro ao enviar, tente novamente");
            //     echo json_encode(["message" => $this->message->render()]);
            //     return;
            // }

            if (!str_verify($data['nome'])) {
                $this->message->warning("Não foi possível validar o Nome. Tente novamente");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }

            if (in_array("", $data)) {
                $this->message->warning("Preencha todos os campos para continuar");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }

            if (!is_email($data['email'])) {
                $this->message->warning("Informe um e-mail válido");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }



            $auth = new Auth();
            $user = new Users();
            $user->bootstrap(
                '1',
                $data['nome'],
                $data['senha'],
                '1',
                '1',
                $data['email'],
                'R'
            );

            if (!$auth->register($user)) {
                echo json_encode(["message" => $auth->message->render()]);
                return;
            } else {
                $json["redirect"] = url("confirma");
            }

            echo json_encode($json);
            return;
        }

        $menu = '<ul class="sub-menu">                    
                    <li>
                        <a href="#con">Contato</a>
                    </li>
                </ul>';

        $btnLogin = '<div class="donate-btn-header">
													<a class="dtbtn" href="' . url('login') . '">Entrar</a>
												</div>';


        $front = [
            "titulo" => "Taskforce",
            "user" => $this->user,
            "hmenu" => $menu,
            "login" => $btnLogin,
        ];

        echo $this->view->render("tcsistemas.home/taskforce/register", [
            "front" => $front
        ]);
    }

    public function confirm()
    {
        $front = [
            "titulo" => "Taskforce"
        ];

        echo $this->view->render("tcsistemas.home/taskforce/confirm", [
            "front" => $front,
            "titulo" => "Taskforce",
            "user" => $this->user
        ]);
    }

    /**
     *
     * @param [type] $data
     * @return void
     */
    public function success($data): void
    {
        $email = ll_decode($data['email']);
        $user = (new Users())->findByEmail($email);

        if ($user && $user->status != "confirmed") {
            $user->status = "C";
            $user->save();
        }

        $front = [
            "titulo" => "Taskforce",
            "user" => $this->user
        ];


        echo $this->view->render("optin", [
            "front" => $front,
            "data" => (object)[
                "title" => "Tudo pronto!",
                "desc" => "Bem vindo(a). Você está um passo mais perto de alcançar resultados extraordinários,<br>garantindo o sucesso do
            seu negócio.",
                "link" => url("login"),
                "linkTitle" => "Fazer login"
            ]
        ]);
    }

    public function recover(?array $data): void
    {
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (empty($data['email'])) {
                $this->message->warning("Informe seu e-mail para recuperar a senha");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }

            if (!is_email($data['email'])) {
                $this->message->warning("Informe um e-mail válido");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }

            $auth = new Auth();
            if ($auth->recover($data['email'])) {
                $json["message"] = $this->message->success("Enviamos um link de recuperação para seu e-mail")->render();
            } else {
                $json["message"] = $auth->message->render();
            }

            echo json_encode($json);
            return;
        }

        $menu = '<ul class="sub-menu">                    
                    <li>
                        <a href="#con">Contato</a>
                    </li>
                </ul>';

        $btnLogin = '<div class="donate-btn-header">
                        <a class="dtbtn" href="' . url('login') . '">Entrar</a>
                    </div>';
        $btnRegister = '<div class="donate-btn-header">
                            <a class="dtbtn" href="' . url('cadastro') . '">Cadastrar</a>
                        </div>';

        $front = [
            "titulo" => "Taskforce",
            "user" => $this->user,
            "hmenu" => $menu,
            "login" => $btnLogin,
            "register" => $btnRegister,
        ];

        echo $this->view->render("tcsistemas.home/taskforce/recover", [
            "front" => $front
        ]);
    }

    /**
     * @param [type] $data
     * @return void
     */
    public function reset($data): void
    {

        $email = ll_decode($data['email']);
        $code = $data['token'];

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            if (empty($data['senha']) || empty($data['senhaRe'])) {
                $this->message->warning("Informe e repita a nova senha para continuar");
                echo json_encode(["message" => $this->message->render()]);
                return;
            }

            $auth = new Auth();

            if ($auth->reset($email, $code, $data['senha'], $data['senhaRe'])) {
                $json["message"] = $this->message->success("Senha atualizada com sucesso")->render();
                $json["redirect"] = url("login");
            } else {
                $json["message"] = $auth->message->render();
            }

            echo json_encode($json);
            return;
        }


        $front = [
            "titulo" => "Taskforce",
            "user" => $this->user
        ];

        echo $this->view->render("tcsistemas.home/taskforce/reset", [
            "front" => $front,
            "code" => $data['token'],
            "email" => ll_encode($email)
        ]);
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
