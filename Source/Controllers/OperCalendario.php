<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Models\Auth;
use Source\Boot\Message;
use Source\Models\Ent;
use Source\Models\Equipamentos;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Servico;
use Source\Models\Users;

class OperCalendario extends OperController
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

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_func = $this->user->id_ent;
        $id_empresa = $this->user->id_emp2;

        // Obter o mês e ano atual
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Calcular os dois últimos dias do mês anterior
        $prevMonth = $currentMonth - 1;
        $prevYear = $currentYear;

        if ($prevMonth == 0) {
            $prevMonth = 12; // Volta para dezembro
            $prevYear--; // Ajusta o ano
        }

        $lastDayPrevMonth = date('t', strtotime("$prevYear-$prevMonth-01"));
        $lastTwoDaysPrevMonth = [$lastDayPrevMonth - 1, $lastDayPrevMonth];

        // Calcular os dois primeiros dias do próximo mês
        $nextMonth = $currentMonth + 1;
        $nextYear = $currentYear;

        if ($nextMonth == 13) {
            $nextMonth = 1; // Avança para janeiro
            $nextYear++; // Ajusta o ano
        }

        $firstTwoDaysNextMonth = [1, 2];

        $os1 = (new Os1())->find()->fetch(true);

        $hoje = date("Y-m-d");
        $hrAtual = date('H:i');
        $hrAtualConvertida = timeToSeconds($hrAtual);

        $os2 = (new Os2())->findByOper($id_func);

        if (!empty($os2)) {
            $ordensPorDia = (new Os2())->find(
                "id_colaborador = :id_colaborador",
                "id_colaborador={$id_func}",
                "dataexec, COUNT(*) AS grupo"
            )->group("dataexec")->fetch(true);


            foreach ($os2 as $os) {
                $ordemPai = (new Os1())->findById($os->id_os1);
                $cliente = (new Ent())->findById($ordemPai->id_cli);
                $servico = (new Servico())->findById($os->id_servico);

                $os->cliente = $cliente->nome;
                $os->servico = $servico->nome;
            }
        } else {
            $ordensPorDia = [];
            $os2 = [];
        }

        $servicos = (new Servico())->find()->fetch(true);        

        $operador = (new Users())->find(
            "tipo = :tipo",
            "tipo=2"
        )->fetch(true);

        $equipamentos = (new Equipamentos())->find()->fetch(true);
        $empresa = (new Ent())->findById($id_empresa);


        $meses = [
            1 => "Janeiro",
            2 => "Fevereiro",
            3 => "Março",
            4 => "Abril",
            5 => "Maio",
            6 => "Junho",
            7 => "Julho",
            8 => "Agosto",
            9 => "Setembro",
            10 => "Outubro",
            11 => "Novembro",
            12 => "Dezembro"
        ];

        $front = [
            "titulo" => "Dashboard - Taskforce",
            "user" => $this->user,
            "nav" => "Calendário",
            "navback" => "oper_dash",
            "navlink" => "oper_calendario"
        ];

        echo $this->view->render("calendario/calendario", [
            "front" => $front,
            "meses" => $meses,
            "lastTwoDaysPrevMonth" => $lastTwoDaysPrevMonth,
            "firstTwoDaysNextMonth" => $firstTwoDaysNextMonth,
            "currentMonth" => $currentMonth,
            "currentYear" => $currentYear,
            "ordensPorDia" => $ordensPorDia,
            "os2" => $os2,
            "operador" => $operador,
            "servico" => $servicos,
            "equipamentos" => $equipamentos,
            "empresa" => $empresa
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




    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
