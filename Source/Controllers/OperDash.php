<?php

namespace Source\Controllers;

use DateTime;
use League\Plates\Engine;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\Emp2;
use Source\Models\EqpMov;
use Source\Models\Os2;
use Source\Models\Users;

class OperDash extends OperController
{
    public function __construct()
    {
        // $this->view = new Engine(CONF_APP_PATH . "Views/tcsistemas.os-oper/", "php");
        // $this->message = new Message();
        // $this->user = Auth::user();

        // if (!$this->user) {
        //     $this->message->error("Para acessar é preciso logar-se")->flash();
        //     redirect("");
        // }

        parent::__construct();
    }

    public function dash(): void
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        $empresa = (new Emp2())->findById($id_empresa);

        $hoje = date("Y-m-d");
        $hrAtual = date('H:i');
        $hrAtualConvertida = timeToSeconds($hrAtual);

        $solicitacoes = (new EqpMov())->find(
            "id_user_destino = :id_user_destino AND status = :status",
            "id_user_destino={$id_user}&status=A"
        )->fetch(true);
        $enviadas = (new EqpMov())->find(
            "id_user_origem = :id_user_origem AND status = :status",
            "id_user_origem={$id_user}&status=A"
        )->fetch(true);

        $os2 = (new Os2())->findByOper($id_func);

        if (!empty($os2)) {
            //** DESCOMENTAR O CÓDIGO ABAIXO PARA TAREFAS DE ORÇAMENTOS NÃO APARECEREM MAIS PRO OPERADOR */
            // $os2 = array_filter($os2, function ($os) {
            //     $os1 = (new Os1())->findById($os->id_os1);
            //     return $os1->id_status != 8; // Exclui tarefas com id_status == 8
            // });

            $pendentes = array_filter($os2, function ($item) use ($hoje) {
                return $item->status == "A" && $item->dataexec <= $hoje;
            });

            $futuras = array_filter($os2, function ($item) use ($hoje) {
                return $item->status == "A" && $item->dataexec > $hoje;
            });

            $andamento = array_filter($os2, function ($item) {
                return $item->status == "I";
            });

            $concluidas = array_filter($os2, function ($item) {
                return $item->status == "C";
            });

            $canceladas = array_filter($os2, function ($item) {
                return $item->status == "D";
            });

            $pausadas = array_filter($os2, function ($item) {
                return $item->status == "P";
            });
        } else {
            $pendentes = [];
            $andamento = [];
            $concluidas = [];
            $canceladas = [];
            $pausadas = [];
            $futuras = [];
        }


        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "secTit" => "Olá, " . $this->user->nome . "!"
        ];

        echo $this->view->render("dash/dash", [
            "front" => $front,
            "empresa" => $empresa,
            "pendentes" => $pendentes,
            "andamento" => $andamento,
            "pausadas" => $pausadas,
            "concluidas" => $concluidas,
            "canceladas" => $canceladas,
            "futuras" => $futuras,
            "os2" => $os2,
            "solicitacoes" => $solicitacoes,
            "enviadas" => $enviadas
        ]);
    }

    public function retornaDias($data)
    {
        $month = isset($_POST['month']) ? (int)$_POST['month'] : date('n');
        $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y'); // Usa o ano enviado ou assume o atual

        // Obter o número de dias no mês atual
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Obter o número de dias no mês anterior
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear--;
        }
        $daysInPrevMonth = cal_days_in_month(CAL_GREGORIAN, $prevMonth, $prevYear);

        // Dois últimos dias do mês anterior
        for ($day = $daysInPrevMonth - 1; $day <= $daysInPrevMonth; $day++) {
            echo "<div class=\"day prev-month\" data-day=\"$day\" data-month=\"$prevMonth\" data-year=\"$prevYear\"><span>$day</span></div>";
        }

        // Dias do mês atual
        for ($day = 1; $day <= $daysInMonth; $day++) {
            echo "<div class=\"day\" data-day=\"$day\" data-month=\"$month\" data-year=\"$year\"><span>$day</span></div>";
        }

        // Dois primeiros dias do mês seguinte
        $nextMonth = $month + 1;
        $nextYear = $year;
        if ($nextMonth > 12) {
            $nextMonth = 1;
            $nextYear++;
        }
        for ($day = 1; $day <= 2; $day++) {
            echo "<div class=\"day next-month\" data-day=\"$day\" data-month=\"$nextMonth\" data-year=\"$nextYear\"><span>$day</span></div>";
        }
    }

    public function oper($data)
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {
            if (!str_verify($data['nome'])) {
                $json['message'] = "ERRO! Caracteres inválidos para o campo 'USUÁRIO'. Tente novamente!";
                echo json_encode($json);
                return;
            }

            $id = ll_decode($data['id_users']);
            $user = (new Users)->findById($id);

            $user->nome = $data['nome'];

            if (!empty($data["senha"])) {
                if (empty($data["senha_re"])) {
                    $json["message"] = "Para alterar sua senha, informe e repita a nova senha!";
                    echo json_encode($json);
                    return;
                } else if ($data["senha"] != $data["senha_re"]) {
                    $json["message"] = "As senhas informadas não conferem!";
                    echo json_encode($json);
                    return;
                }

                $user->senha = $data["senha"];
            }

            if (!$user->save) {
                $json['message'] = $this->message->warning($user->fail()->getMessage());
                echo json_encode($json);
                return;
            } else {
                $json['success'] = "Atualizado com sucesso!";
                $json['reload'] = true;
                echo json_encode($json);
                return;
            }
        }


        $front = [
            "titulo" => "Operador - Taskforce",
            "user" => $this->user,
            "nav" => "Operador",
            "navback" => "oper_dash",
            "navlink" => "oper_dash/oper",
        ];

        echo $this->view->render("dash/profile", [
            "front" => $front
        ]);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
