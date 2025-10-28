<?php

namespace Source\Controllers;

use Source\Models\Horas;
use Source\Models\Log;
use Source\Models\Turno;

class TurnoController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->cadastros != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function turno(): void
    {
        $id_user = $this->user->id_users;
        $id_emp = $this->user->id_emp;

        $front = [
            "titulo" => "TURNOS - TC SISTEMAS",
            "user" => $this->user,
            "tituloPai" => "Cadastros/",
            "secTit" => "Turnos"
        ];

        echo $this->view->render("tcsistemas.os/turno/turno", [
            "front" => $front
        ]);
    }

    public function index(): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        $turnoDefault = (new Turno())->findById(13);
        $turnosGerais = (new Turno())->find()->fetch(true);

        $turno = [];

        if ($turnoDefault) {
            $turno[] = $turnoDefault;
        }

        if (!empty($turnosGerais)) {
            $turno = array_merge($turno, $turnosGerais);
        }

        
        $front = [
            "titulo" => "Turnos - Taskforce",
            "user" => $this->user,
            "secTit" => "Turnos"
        ];

        echo $this->view->render("tcsistemas.os/turno/turnoList", [
            "turnos" => $turno,
            "front" => $front
        ]);
    }

    public function form(?array $data): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        $turnos = "";
        $horas = "";
        $diasSelecionados = "";
        $secTit = "Cadastrar Turno";
        if (isset($data['id_turno'])) {
            $id_turno = ll_decode($data['id_turno']);
            if (ll_intValida($id_turno)) {
                $turnos = (new Turno())->findById($id_turno);
                $horas = (new Horas())->find("id_turno = :id_turno", "id_turno={$id_turno}", "*", false)->fetch(true);
                $diasSelecionados = $turnos->dia_semana;
            }
            $secTit = "Visualizar/Editar Turno";
        }

        $front = [
            "titulo" => "TURNOS - TC SISTEMAS",
            "user" => $this->user,            
            "secTit" => $secTit
        ];

        echo $this->view->render("tcsistemas.os/turno/turnoCad", [
            "turnos" => $turnos,
            "horas" => $horas,
            "dias" => $diasSelecionados,
            "front" => $front
        ]);
    }



    public function salvar(array $data): void
    {
        $id_user = $this->user->id_users;
        $id_empresa = $this->user->id_emp2;

        // var_dump($data);

        // exit;


        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            if (!filter_var($data['nome'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'NOME'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (!filter_var($data['hora_ini'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("Por favor, informe um horário válido")->render();
                echo json_encode($json);
                return;
            }

            if (!filter_var($data['hora_fim'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("Por favor, informe um horário válido")->render();
                echo json_encode($json);
                return;
            }

            $dias = set_indice_dias($data['dia_semana']);

            if (isset($dias[7]) && count($dias) > 1) {
                $json['message'] = $this->message->warning("O valor 'Segunda à Sexta' não pode estar marcado em conjunto com outras opções. Verifique!")->render();
                echo json_encode($json);
                return;
            }

            if (isset($dias[8]) && count($dias) > 1) {
                $json['message'] = $this->message->warning("O valor 'Segunda à Sábado' não pode estar marcado em conjunto com outras opções. Verifique!")->render();
                echo json_encode($json);
                return;
            }

            if (count($dias) < 1) {
                $json['message'] = $this->message->warning("Selecione um dia!")->render();
                echo json_encode($json);
                return;
            }

            $id_turno = ll_decode($data['id_turno']);            

            if (ll_intValida($id_turno)) {
                $turno = (new Turno())->findById($id_turno);
                $antes = clone $turno->data();
                $acao = "U";
                $horasExcluir = (new Horas())->find("id_turno = :id_turno", "id_turno={$id_turno}", "*", false)->fetch(true);
                if ($horasExcluir) {
                    foreach ($horasExcluir as $regExc) {
                        $excluir = (new Horas)->findById($regExc->id);
                        $excluir->destroy();
                    }
                }
            } else {
                $turno = new Turno();
                $antes = null;
                $acao = "C";
            }

            $int_ini = "";
            $int_fim = "";
            if ($data['intervalo_ini'] != "") {
                $int_ini = $data['intervalo_ini'];
            }

            if ($data['intervalo_ini'] != "") {
                $int_fim = $data['intervalo_fim'];
            }

            $turno->id_emp2 = $id_empresa;
            $turno->id_users = $id_user;
            $turno->nome = $data['nome'];
            $turno->descricao = $data['descricao'];
            $turno->carga = $data['carga'];
            $turno->hora_ini = $data['hora_ini'];
            $turno->hora_fim = $data['hora_fim'];
            if ($int_ini != "") {
                $turno->intervalo_ini = $int_ini;
            }
            if ($int_fim != "") {
                $turno->intervalo_fim = $int_fim;
            }
            $turno->segunda = (isset($data['segunda']) && $data['segunda'] === 'on') ? 1 : 0;
            $turno->terca = (isset($data['terca']) && $data['terca'] === 'on') ? 1 : 0;
            $turno->quarta = (isset($data['quarta']) && $data['quarta'] === 'on') ? 1 : 0;
            $turno->quinta = (isset($data['quinta']) && $data['quinta'] === 'on') ? 1 : 0;
            $turno->sexta = (isset($data['sexta']) && $data['sexta'] === 'on') ? 1 : 0;
            $turno->sabado = (isset($data['sabado']) && $data['sabado'] === 'on') ? 1 : 0;
            $turno->domingo = (isset($data['domingo']) && $data['domingo'] === 'on') ? 1 : 0;

            if (!isset($dias[7]) || !isset($dias[8])) {
                $indices = array_keys($dias);
                $diasBanco = implode(', ', $indices);
                $turno->dia_semana = $diasBanco;
            } elseif (isset($dias[7])) {
                $turno->dia_semana = '7';
            } elseif (isset($dias[8])) {
                $turno->dia_semana = '8';
            }

            if (!$turno->save) {
                $json['message'] = $this->message->error("Erro ao cadastrar, por favor verifique os dados!")->render();
                echo json_encode($json);
                return;
            }

            $log = new Log();
            $log->registrarLog($acao, $turno->getEntity(), $turno->id, $antes, $turno->data());

            if ($turno->segunda == 1) {

                $int_ini_mon = "";
                $int_fim_mon = "";
                if ($data['intervalo_ini_mon'] != "") {
                    $int_ini_mon = $data['intervalo_ini_mon'];
                }

                if ($data['intervalo_ini_mon'] != "") {
                    $int_fim_mon = $data['intervalo_fim_mon'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 0;
                $horas->hora_ini = $data['hora_ini_mon'];
                $horas->hora_fim = $data['hora_fim_mon'];
                if ($int_ini_mon != "") {
                    $horas->intervalo_ini = $int_ini_mon;
                }
                if ($int_fim_mon != "") {
                    $horas->intervalo_fim = $int_fim_mon;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas da segunda, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->terca == 1) {

                $int_ini_tue = "";
                $int_fim_tue = "";
                if ($data['intervalo_ini_tue'] != "") {
                    $int_ini_tue = $data['intervalo_ini_tue'];
                }

                if ($data['intervalo_ini_tue'] != "") {
                    $int_fim_tue = $data['intervalo_fim_tue'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 1;
                $horas->hora_ini = $data['hora_ini_tue'];
                $horas->hora_fim = $data['hora_fim_tue'];
                if ($int_ini_tue != "") {
                    $horas->intervalo_ini = $int_ini_tue;
                }
                if ($int_fim_tue != "") {
                    $horas->intervalo_fim = $int_fim_tue;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas da terça, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->quarta == 1) {

                $int_ini_wed = "";
                $int_fim_wed = "";
                if ($data['intervalo_ini_wed'] != "") {
                    $int_ini_wed = $data['intervalo_ini_wed'];
                }

                if ($data['intervalo_ini_wed'] != "") {
                    $int_fim_wed = $data['intervalo_fim_wed'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 2;
                $horas->hora_ini = $data['hora_ini_wed'];
                $horas->hora_fim = $data['hora_fim_wed'];
                if ($int_ini_wed != "") {
                    $horas->intervalo_ini = $int_ini_wed;
                }
                if ($int_fim_wed != "") {
                    $horas->intervalo_fim = $int_fim_wed;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas da quarta, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->quinta == 1) {

                $int_ini_thu = "";
                $int_fim_thu = "";
                if ($data['intervalo_ini_thu'] != "") {
                    $int_ini_thu = $data['intervalo_ini_thu'];
                }

                if ($data['intervalo_ini_thu'] != "") {
                    $int_fim_thu = $data['intervalo_fim_thu'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 3;
                $horas->hora_ini = $data['hora_ini_thu'];
                $horas->hora_fim = $data['hora_fim_thu'];
                if ($int_ini_thu != "") {
                    $horas->intervalo_ini = $int_ini_thu;
                }
                if ($int_fim_thu != "") {
                    $horas->intervalo_fim = $int_fim_thu;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas da quinta, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->sexta == 1) {

                $int_ini_fri = "";
                $int_fim_fri = "";
                if ($data['intervalo_ini_fri'] != "") {
                    $int_ini_fri = $data['intervalo_ini_fri'];
                }

                if ($data['intervalo_ini_fri'] != "") {
                    $int_fim_fri = $data['intervalo_fim_fri'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 4;
                $horas->hora_ini = $data['hora_ini_fri'];
                $horas->hora_fim = $data['hora_fim_fri'];
                if ($int_ini_fri != "") {
                    $horas->intervalo_ini = $int_ini_fri;
                }
                if ($int_fim_fri != "") {
                    $horas->intervalo_fim = $int_fim_fri;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas da sexta, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->sabado == 1) {

                $int_ini_sat = "";
                $int_fim_sat = "";
                if ($data['intervalo_ini_sat'] != "") {
                    $int_ini_sat = $data['intervalo_ini_sat'];
                }

                if ($data['intervalo_ini_sat'] != "") {
                    $int_fim_sat = $data['intervalo_fim_sat'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 5;
                $horas->hora_ini = $data['hora_ini_sat'];
                $horas->hora_fim = $data['hora_fim_sat'];
                if ($int_ini_sat != "") {
                    $horas->intervalo_ini = $int_ini_sat;
                }
                if ($int_fim_sat != "") {
                    $horas->intervalo_fim = $int_fim_sat;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas do sábado, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
            if ($turno->domingo == 1) {

                $int_ini_sun = "";
                $int_fim_sun = "";
                if ($data['intervalo_ini_sun'] != "") {
                    $int_ini_sun = $data['intervalo_ini_sun'];
                }
                if ($data['intervalo_ini_sun'] != "") {
                    $int_fim_sun = $data['intervalo_fim_sun'];
                }

                $horas = new Horas();

                $horas->id_emp2 = $id_empresa;
                $horas->id_users = $id_user;
                $horas->id_turno = $turno->id;
                $horas->dia_semana = 6;
                $horas->hora_ini = $data['hora_ini_sun'];
                $horas->hora_fim = $data['hora_fim_sun'];
                if ($int_ini_sun != "") {
                    $horas->intervalo_ini = $int_ini_sun;
                }
                if ($int_fim_sun != "") {
                    $horas->intervalo_fim = $int_fim_sun;
                }

                if (!$horas->save) {
                    $json['message'] = $this->message->error("Erro ao cadastrar as horas do domingo, por favor verifique os dados!")->render();
                    echo json_encode($json);
                    return;
                }
            }
        }

        if (ll_intValida($id_turno)) {
            $this->message->success("REGISTRO ALTERADO COM SUCESSO")->flash();
        } else {
            $this->message->success("CADASTRADO COM SUCESSO!")->flash();
        }
        $json["redirect"] = url("turno");
        echo json_encode($json);
    }

    public function excluir($data): void
    {
        $id_turno = ll_decode($data['id_turno']);

        if (ll_intValida($id_turno)) {
            $turno = (new Turno())->findById($id_turno);
            $antes = clone $turno->data();
            $horasExcluir = (new Horas())->find("id_turno = :id_turno", "id_turno={$id_turno}", "*", false)->fetch(true);
            if (!empty($horasExcluir)) {
                foreach ($horasExcluir as $regExc) {
                    $excluir = (new Horas)->findById($regExc->id);
                    $excluir->destroy();
                }
            }

            if ($turno->destroy()) {
                $this->message->warning("REGISTRO EXCLUÍDO COM SUCESSO")->flash();
                $json["redirect"] = url("turno");
                echo json_encode($json);
            }
            $log = new Log();
            $log->registrarLog("D", $turno->getEntity(), $turno->id, $antes, null);
        }
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
