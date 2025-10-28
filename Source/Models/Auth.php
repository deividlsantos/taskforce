<?php

namespace Source\Models;

use League\Plates\Engine;
use Source\Boot\Email;
use Source\Boot\Session;
use Source\Boot\Message;



class Auth extends Users
{
    /** @var Message|null */
    protected $message;

    public function __construct()
    {
        parent::__construct("users", ["id_emp2", "nome", "nivel", "email"]);
        $this->message = new Message();
    }

    /**
     * @return Users|null
     */
    public static function user(): ?Users
    {
        $session = new Session();
        if (!$session->has("authUser")) {
            return null;
        }

        $user = (new Users())->findById2($session->authUser);

        // Se houver uma empresa ativa na sessão, sobrescreve o id_emp2
        if ($user && $session->has("authEmp")) {
            $user->id_emp2 = $session->authEmp;
        }

        return $user;
    }

    /**
     * log-out
     * @return void
     */
    public static function logout(): void
    {
        $session = new Session();
        $session->unset("authUser");
    }

    public function message(): ?Message
    {
        return $this->message;
    }

    public function register(Users $user): bool
    {
        if (!$user->save()) {
            $this->message->warning($user->fail()->getMessage());
            return false;
        }

        $view = new Engine(CONF_APP_PATH . "Views/email", "php");
        $message = $view->render("confirm", [
            "titulo" => "Confirme seu cadastro",
            "usuario" => $user->nome,
            "confirm_link" => url("obrigado/" . ll_encode($user->email))
        ]);

        $email = (new Email())->bootstrap(
            "Confirme seu cadastro",
            $message,
            $user->email,
            $user->nome
        );

        if (!$email->send()) {
            $this->message->error($email->message());
            return false;
        }
        return true;
    }

    public function login(string $email, string $senha, bool $save = false): ?bool
    {
        if (!is_email($email)) {
            $this->message->warning("O e-mail informado não é válido");
            return false;
        }

        if ($save) {
            setcookie("authEmail", $email, time() + 604800, "/");
        } else {
            setcookie("authEmail", "", time() - 3600, "/");
        }

        if (!is_passwd($senha)) {
            $this->message->warning("A senha informada não é válida");
            return false;
        }

        $user = (new Users())->findByEmail($email);

        if (!$user) {
            $this->message->error("O email informado não está cadastrado");
            return false;
        }

        if (!passwd_verify($senha, $user->senha)) {
            $this->message->error("A senha informada não confere");
            return false;
        }

        if (passwd_rehash($user->senha)) {
            $user->senha = $senha;
            $user->save();
        }

        //LOGIN
        $session = new Session();
        $session->set("authUser", $user->id);
        if ($user->tipo == 5) {
            $authEmp = $user->last_emp ?: $user->id_emp2;
        } else {
            $authEmp = $user->id_emp2;
        }
        $empresa = (new Emp2())->findById($authEmp);
        set_session($session, $empresa);
        $this->message->success("Login efetuado com sucesso")->flash();

        return true;
    }

    /**
     * @param string $email
     * @return boolean
     */
    public function recover(string $email): bool
    {
        $user = (new Users())->findByEmail($email);
        if (!$user) {
            $this->message->warning("E-mail não cadastrado");
            return false;
        }

        $user->forget = md5(uniqid(rand(), true));
        $user->save();

        $view = new Engine(CONF_APP_PATH . "Views/email", "php");

        $userMail = ll_encode($user->email);

        $message = $view->render("recover", [
            "titulo" => "Recupere sua senha",
            "usuario" => $user->nome,
            "recover_link" => url("recuperar/{$userMail}/{$user->forget}")
        ]);

        $email = (new Email())->bootstrap(
            "Recupere sua senha",
            $message,
            $user->email,
            $user->nome
        );

        if (!$email->send()) {
            $this->message->error($email->message());
            echo json_encode(["message" => $this->message->render()]);
            return false;
        }

        return true;
    }

    /**
     * @param string $email
     * @param string $code
     * @param string $senha
     * @param string $senhaRe
     * @return boolean
     */
    public function reset(string $email, string $code, string $senha, string $senhaRe): bool
    {
        $user = (new Users())->findByEmail($email);
        if (!$user) {
            $this->message->warning("Usuário não encontrado");
            return false;
        }

        if ($user->forget != $code) {
            $this->message->warning("Código de recuperação inválido");
            return false;
        }

        if (!is_passwd($senha)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("A senha deve ter entre {$min} e {$max} caracteres");
            return false;
        }

        if ($senha != $senhaRe) {
            $this->message->warning("As senhas não conferem");
            return false;
        }

        $user->senha = $senha;
        $user->forget = null;
        $user->save();

        $this->message->success("Senha atualizada com sucesso")->flash();
        return true;
    }
}
