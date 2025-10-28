<?php

namespace Source\Controllers;


use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\EntFun;
use Source\Models\Faltas;
use Source\Models\Feriados;
use Source\Models\Horas;
use Source\Models\Log;
use Source\Models\Ponto1;
use Source\Models\Ponto2;
use Source\Models\Turno;

class PontoController extends Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->user->id_emp2 != 1 && $this->user->ponto != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {

        $front = [
            "titulo" => "Cartões de Ponto - Taskforce",
            "user" => $this->user,
            "tituloPai" => "Cartões de Ponto"
        ];
        echo $this->view->render("tcsistemas.ponto/ponto/index", [
            "front" => $front
        ]);
    }

    public function fechamento($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $func = (new Ent())->find(
            "status = :status AND tipo = :tipo",
            "status=A&tipo=3"
        )->fetch(true);

        $front = [
            "titulo" => "Cartões de Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => "Gerar Registros de Ponto"
        ];

        echo $this->view->render("tcsistemas.ponto/ponto/pontoGerar", [
            "func" => $func,
            "front" => $front
        ]);
    }

    /**
     * $calendario - Segunda = 0, Terça = 1, Quarta = 2, Quinta = 3, Sexta = 4, Sábado = 5, Domingo = 6
     * $turno->dia_semana = Segunda = 0, Terça = 1, Quarta = 2, Quinta = 3, Sexta = 4, Sábado = 5, Domingo = 6, Segunda a Sexta = 7, Segunda a Sábado = 8
     * @param mixed $data
     * @return void
     */
    public function gerar($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (!isset($data['id_func'])) {
            $json = $this->message->warning("Selecione um funcionário")->render();
            echo $json;
            return;
        }

        if (empty($data['mes']) || empty($data['ano'])) {
            $json = $this->message->warning("Selecione um período")->render();
            echo $json;
            return;
        }

        $mesAtual = date('m');
        $anoAtual = date('Y');

        if (($data['mes']) > $mesAtual && $data['ano'] >= $anoAtual) {
            $json = $this->message->warning("Mês de competência não pode ser maior que o mês atual.")->render();
            echo $json;
            return;
        }

        /**
         * VERIFICAÇÃO NO FORMATO DO VALOR COLOCADO NO CAMPO FÉRIAS DE CADA FUNCIONÁRIO
         * SE NÃO TIVER VALOR O CÓDIGO CONTINUA
         */
        foreach ($data as $key => $value) {
            if (preg_match('/^date_range_(\d+)$/', $key, $matches)) {
                if ($value !== "") {
                    $periodoInvalido = validateDateRange($value, $data['mes'], $data['ano']);
                    if ($periodoInvalido) {
                        $json = $this->message->warning("Erro no funcionário ID {$matches[1]}: {$periodoInvalido}")->render();
                        echo $json;
                        return;
                    }
                }
            }
        }

        $year = $data['ano'];
        $month = $data['mes']; // mantido por estrutura

        $feriadosBanco = new Feriados;

        // Parte comum da cláusula WHERE, com base nas regras
        $where = "(padrao = 1 OR (padrao != 1 AND (recorrente = 1 OR (recorrente != 1 AND ano = :ano))))";
        $params = "ano={$year}";

        // Condições adicionais por empresa/usuário
        if ($id_empresa == 1) {
            $where = "(id_emp2 = :default OR id_users = :id_users) AND $where";
            $params .= "&default=1&id_users={$id_user}";
        } else {
            $where = "id_emp2 IN (:default, :id_emp2) AND $where";
            $params .= "&default=1&id_emp2={$id_empresa}";
        }

        // Consulta final
        $dias = $feriadosBanco->find($where, $params, "*", false)->fetch(true);        

        $calendario = retornaCalendario($year, $month, $dias);
        $funcionarios = $data['id_func'];
        $codSabDom = [5, 6];        

        /**
         * INÍCIO DO FOREACH PRA CADA FUNCIONÁRIO
         * $ponto1 É VARIÁVEL GERADA PELO BANCO DE DADOS
         */
        foreach ($funcionarios as $id_func) {
            $turno_func = (new EntFun())->find("id_ent = :id_ent", "id_ent={$id_func}", "*", false)->fetch()->id_turno;
            $turno = (new Turno())->findById($turno_func);

            $horaSegunda = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=0", "*", false)->fetch();
            $horaTerca = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=1", "*", false)->fetch();
            $horaQuarta = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=2", "*", false)->fetch();
            $horaQuinta = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=3", "*", false)->fetch();
            $horaSexta = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=4", "*", false)->fetch();
            $horaSabado = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=5", "*", false)->fetch();
            $horaDomingo = (new Horas())->find("id_turno = :id_turno AND dia_semana = :dia_semana", "id_turno={$turno_func}&dia_semana=6", "*", false)->fetch();


            $ferias = false;
            $chaveFerias = 'date_range_' . $id_func;
            if ($data[$chaveFerias] != "") {
                $periodoFerias = $data[$chaveFerias];
                list($feriasIni, $feriasFim) = explode('-', $periodoFerias);
                $ferias = true;
            }

            $ponto1 = new Ponto1();
            $ponto1->id_emp2 = $id_empresa;
            $ponto1->id_func = $id_func;
            $ponto1->id_turno = $turno_func;
            $ponto1->dia_semana = $turno->dia_semana;
            $ponto1->mes = $data['mes'];
            $ponto1->ano = $data['ano'];
            $ponto1->sabado = $turno->sabado;
            $ponto1->domingo = $turno->domingo;
            $ponto1->id_users = $id_user;

            if (!$ponto1->save) {
                //$json = $this->message->error("ERRO AO CADASTRAR CABEÇALHO PONTO.")->render();
                $json = $this->message->warning($ponto1->fail()->getMessage())->render();
                echo $json;
                return;
            }

            $log = new Log();
            $log->registrarLog("C", $ponto1->getEntity(), $ponto1->id, null, $ponto1->data());

            /**
             * INÍCIO DO FOREACH PRA QUANTIDADE DE DIAS DO MÊS
             * E ATRIBUIÇÃO DO DIA DA SEMANA PRA CADA DIA DO MÊS
             * $calendario É UMA VARIÁVEL GERADA PELO PHP
             */
            foreach ($calendario as $regDia) {
                list($ano, $mes, $dia) = explode("-", $regDia['data']);
                $ponto2 = new Ponto2();
                $ponto2->id_users = $id_user;
                $ponto2->id_emp2 = $id_empresa;
                $ponto2->id_ponto1 = $ponto1->id;
                $ponto2->dia = $dia;
                $ponto2->nome_dia_semana = traduzirDiaSemana($regDia['dia_semana']);


                if ($ferias && $dia >= $feriasIni && $dia <= $feriasFim) { // Verifica se o dia está dentro do período de férias                    
                    $ponto2->hora_ini = "FÉRIAS";
                    $ponto2->hora_fim = "FÉRIAS";
                    $ponto2->intervalo_ini = "FÉRIAS";
                    $ponto2->intervalo_fim = "FÉRIAS";
                } elseif (!is_null($regDia['feriado'])) { // Verifica se o dia é um feriado                    
                    $ponto2->hora_ini = "FERIADO";
                    $ponto2->hora_fim = "FERIADO";
                    $ponto2->intervalo_ini = "FERIADO";
                    $ponto2->intervalo_fim = "FERIADO";
                } elseif ($regDia['cod_dia_semana'] == 5) { // Verifica se o dia no loop é um sábado
                    if (str_contem($turno->dia_semana, '5') || str_contem($turno->dia_semana, '8')) { //verifica se o sábado está no turno do funcionário
                        if ($turno->sabado == 1) { //verifica se o sábado tem horário especial
                            $ponto2->hora_ini = $horaSabado->hora_ini;
                            $ponto2->hora_fim = $horaSabado->hora_fim;
                            $ponto2->intervalo_ini = $horaSabado->intervalo_ini;
                            $ponto2->intervalo_fim = $horaSabado->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    } else {
                        $ponto2->hora_ini = "SÁBADO";
                        $ponto2->hora_fim = "SÁBADO";
                        $ponto2->intervalo_ini = "SÁBADO";
                        $ponto2->intervalo_fim = "SÁBADO";
                    }
                } elseif ($regDia['cod_dia_semana'] == 6) { // Verifica se o dia no loop é um domingo                    
                    if (str_contem($turno->dia_semana, '6')) { //verifica se o domingo está no turno do funcionário
                        if ($turno->domingo == 1) { //verifica se o domingo tem horário especial
                            $ponto2->hora_ini = $horaDomingo->hora_ini;
                            $ponto2->hora_fim = $horaDomingo->hora_fim;
                            $ponto2->intervalo_ini = $horaDomingo->intervalo_ini;
                            $ponto2->intervalo_fim = $horaDomingo->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    } else {
                        $ponto2->hora_ini = "DOMINGO";
                        $ponto2->hora_fim = "DOMINGO";
                        $ponto2->intervalo_ini = "DOMINGO";
                        $ponto2->intervalo_fim = "DOMINGO";
                    }
                } elseif ($regDia['cod_dia_semana'] == 0) { // Verifica se o dia no loop é segunda                    
                    if (str_contem($turno->dia_semana, '0') || str_contem($turno->dia_semana, '7') || str_contem($turno->dia_semana, '8')) { //verifica se a segunda está no turno do funcionário
                        if ($turno->segunda == 1) { //verifica se a segunda tem horário especial
                            $ponto2->hora_ini = $horaSegunda->hora_ini;
                            $ponto2->hora_fim = $horaSegunda->hora_fim;
                            $ponto2->intervalo_ini = $horaSegunda->intervalo_ini;
                            $ponto2->intervalo_fim = $horaSegunda->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    }
                } elseif ($regDia['cod_dia_semana'] == 1) { // Verifica se o dia no loop é terça                    
                    if (str_contem($turno->dia_semana, '1') || str_contem($turno->dia_semana, '7') || str_contem($turno->dia_semana, '8')) { //verifica se a terça está no turno do funcionário
                        if ($turno->terca == 1) { //verifica se a terça tem horário especial
                            $ponto2->hora_ini = $horaTerca->hora_ini;
                            $ponto2->hora_fim = $horaTerca->hora_fim;
                            $ponto2->intervalo_ini = $horaTerca->intervalo_ini;
                            $ponto2->intervalo_fim = $horaTerca->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    }
                } elseif ($regDia['cod_dia_semana'] == 2) { // Verifica se o dia no loop é quarta                    
                    if (str_contem($turno->dia_semana, '2') || str_contem($turno->dia_semana, '7') || str_contem($turno->dia_semana, '8')) { //verifica se a quarta está no turno do funcionário
                        if ($turno->quarta == 1) { //verifica se a quarta tem horário especial
                            $ponto2->hora_ini = $horaQuarta->hora_ini;
                            $ponto2->hora_fim = $horaQuarta->hora_fim;
                            $ponto2->intervalo_ini = $horaQuarta->intervalo_ini;
                            $ponto2->intervalo_fim = $horaQuarta->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    }
                } elseif ($regDia['cod_dia_semana'] == 3) { // Verifica se o dia no loop é quinta                    
                    if (str_contem($turno->dia_semana, '3') || str_contem($turno->dia_semana, '7') || str_contem($turno->dia_semana, '8')) { //verifica se a quinta está no turno do funcionário
                        if ($turno->quinta == 1) { //verifica se a quinta tem horário especial
                            $ponto2->hora_ini = $horaQuinta->hora_ini;
                            $ponto2->hora_fim = $horaQuinta->hora_fim;
                            $ponto2->intervalo_ini = $horaQuinta->intervalo_ini;
                            $ponto2->intervalo_fim = $horaQuinta->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    }
                } elseif ($regDia['cod_dia_semana'] == 4) { // Verifica se o dia no loop é sexta                    
                    if (str_contem($turno->dia_semana, '4') || str_contem($turno->dia_semana, '7') || str_contem($turno->dia_semana, '8')) { //verifica se a sexta está no turno do funcionário
                        if ($turno->sexta == 1) { //verifica se a sexta tem horário especial
                            $ponto2->hora_ini = $horaSexta->hora_ini;
                            $ponto2->hora_fim = $horaSexta->hora_fim;
                            $ponto2->intervalo_ini = $horaSexta->intervalo_ini;
                            $ponto2->intervalo_fim = $horaSexta->intervalo_fim;
                        } else {
                            $ponto2->hora_ini = $turno->hora_ini;
                            $ponto2->hora_fim = $turno->hora_fim;
                            $ponto2->intervalo_ini = $turno->intervalo_ini;
                            $ponto2->intervalo_fim = $turno->intervalo_fim;
                        }
                    }
                } else {
                    $ponto2->hora_ini = null;
                    $ponto2->hora_fim = null;
                    $ponto2->intervalo_ini = null;
                    $ponto2->intervalo_fim = null;
                }

                //Salva a instância de Ponto2
                if (!$ponto2->save()) {
                    $json = $this->message->error("ERRO AO CADASTRAR PONTOS DIÁRIOS")->render();
                    echo $json;
                    return;
                }
            }
        }

        $json = $this->message->success("PONTO REGISTRADO COM SUCESSO")->render();
        echo $json;
    }

    public function folhas(?array $data): void
    {

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $result = "";
        if (isset($data['mes']) && isset($data['ano'])) {
            $mes = ll_decode($data['mes']);
            $ano = ll_decode($data['ano']);
            $pontos = (new Ponto1())->find(
                "mes = :mes AND ano = :ano",
                "mes={$mes}&ano={$ano}"
            )->fetch(true);
            $result = $pontos;
        }
        $func = (new Ent())->find(
            "tipo = :tipo",
            "tipo=3"
        )->fetch(true);

        $front = [
            "titulo" => "Cartões de Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => "Visualizar Folhas de Ponto"
        ];

        echo $this->view->render("tcsistemas.ponto/ponto/pontoFolhaView", [
            "result" => $result,
            "func" => $func,
            "front" => $front
        ]);
    }

    public function editFolhas($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_ponto1 = ll_decode($data['id_ponto1']);
        $ponto1 = (new Ponto1())->findById($id_ponto1);

        $func = (new Ent())->findById($ponto1->id_func);

        $turno = (new Turno())->findById($ponto1->id_turno);

        $iniTurno = "";
        $intIniTurno = "";
        $intFimTurno = "";
        $fimTurno = "";
        if (validaHoraPonto(substr($turno->hora_ini, 0, 5))) {
            $iniTurno = substr($turno->hora_ini, 0, 5);
        }
        if (validaHoraPonto(substr($turno->hora_fim, 0, 5))) {
            $fimTurno = substr($turno->hora_fim, 0, 5);
        }
        if (validaHoraPonto(substr($turno->intervalo_ini, 0, 5))) {
            $intIniTurno = substr($turno->intervalo_ini, 0, 5);
        }
        if (validaHoraPonto(substr($turno->intervalo_fim, 0, 5))) {
            $intFimTurno = substr($turno->intervalo_fim, 0, 5);
        }

        list($hrsTurno, $minTurno) = calcHorasTrabalhadas($iniTurno, $intIniTurno, $intFimTurno, $fimTurno);
        $totalTurno = $hrsTurno . ":" . $minTurno;

        $segunda = "";
        $terca = "";
        $quarta = "";
        $quinta = "";
        $sexta = "";
        $sabado = "";
        $domingo = "";
        $totalSeg = "";
        $totalTer = "";
        $totalQua = "";
        $totalQui = "";
        $totalSex = "";
        $totalSat = "";
        $totalSun = "";

        if ($turno->segunda) {
            $segunda = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=0"
            )->fetch()->data();
            $iniSeg = "";
            $intIniSeg = "";
            $intFimSeg = "";
            $fimSeg = "";
            if (validaHoraPonto(substr($segunda->hora_ini, 0, 5))) {
                $iniSeg = substr($segunda->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($segunda->hora_fim, 0, 5))) {
                $fimSeg = substr($segunda->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($segunda->intervalo_ini, 0, 5))) {
                $intIniSeg = substr($segunda->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($segunda->intervalo_fim, 0, 5))) {
                $intFimSeg = substr($segunda->intervalo_fim, 0, 5);
            }

            list($hrsSeg, $minSeg) = calcHorasTrabalhadas($iniSeg, $intIniSeg, $intFimSeg, $fimSeg);
            $totalSeg = $hrsSeg . ":" . $minSeg;
        }

        if ($turno->terca) {
            $terca = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=1"
            )->fetch()->data();
            $iniTer = "";
            $intIniTer = "";
            $intFimTer = "";
            $fimTer = "";
            if (validaHoraPonto(substr($terca->hora_ini, 0, 5))) {
                $iniTer = substr($terca->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($terca->hora_fim, 0, 5))) {
                $fimTer = substr($terca->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($terca->intervalo_ini, 0, 5))) {
                $intIniTer = substr($terca->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($terca->intervalo_fim, 0, 5))) {
                $intFimTer = substr($terca->intervalo_fim, 0, 5);
            }

            list($hrsTer, $minTer) = calcHorasTrabalhadas($iniTer, $intIniTer, $intFimTer, $fimTer);
            $totalTer = $hrsTer . ":" . $minTer;
        }

        if ($turno->quarta) {
            $quarta = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=2"
            )->fetch()->data();
            $iniQua = "";
            $intIniQua = "";
            $intFimQua = "";
            $fimQua = "";
            if (validaHoraPonto(substr($quarta->hora_ini, 0, 5))) {
                $iniQua = substr($quarta->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($quarta->hora_fim, 0, 5))) {
                $fimQua = substr($quarta->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($quarta->intervalo_ini, 0, 5))) {
                $intIniQua = substr($quarta->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($quarta->intervalo_fim, 0, 5))) {
                $intFimQua = substr($quarta->intervalo_fim, 0, 5);
            }

            list($hrsQua, $minQua) = calcHorasTrabalhadas($iniQua, $intIniQua, $intFimQua, $fimQua);
            $totalQua = $hrsQua . ":" . $minQua;
        }

        if ($turno->quinta) {
            $quinta = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=3"
            )->fetch()->data();
            $iniQui = "";
            $intIniQui = "";
            $intFimQui = "";
            $fimQui = "";
            if (validaHoraPonto(substr($quinta->hora_ini, 0, 5))) {
                $iniQui = substr($quinta->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($quinta->hora_fim, 0, 5))) {
                $fimQui = substr($quinta->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($quinta->intervalo_ini, 0, 5))) {
                $intIniQui = substr($quinta->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($quinta->intervalo_fim, 0, 5))) {
                $intFimQui = substr($quinta->intervalo_fim, 0, 5);
            }

            list($hrsQui, $minQui) = calcHorasTrabalhadas($iniQui, $intIniQui, $intFimQui, $fimQui);
            $totalQui = $hrsQui . ":" . $minQui;
        }

        if ($turno->sexta) {
            $sexta = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=4"
            )->fetch()->data();
            $iniSex = "";
            $intIniSex = "";
            $intFimSex = "";
            $fimSex = "";
            if (validaHoraPonto(substr($sexta->hora_ini, 0, 5))) {
                $iniSex = substr($sexta->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sexta->hora_fim, 0, 5))) {
                $fimSex = substr($sexta->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($sexta->intervalo_ini, 0, 5))) {
                $intIniSex = substr($sexta->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sexta->intervalo_fim, 0, 5))) {
                $intFimSex = substr($sexta->intervalo_fim, 0, 5);
            }

            list($hrsSex, $minSex) = calcHorasTrabalhadas($iniSex, $intIniSex, $intFimSex, $fimSex);
            $totalSex = $hrsSex . ":" . $minSex;
        }


        if ($turno->sabado) {
            $sabado = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=5"
            )->fetch()->data();
            $iniSat = "";
            $intIniSat = "";
            $intFimSat = "";
            $fimSat = "";
            if (validaHoraPonto(substr($sabado->hora_ini, 0, 5))) {
                $iniSat = substr($sabado->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->hora_fim, 0, 5))) {
                $fimSat = substr($sabado->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->intervalo_ini, 0, 5))) {
                $intIniSat = substr($sabado->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->intervalo_fim, 0, 5))) {
                $intFimSat = substr($sabado->intervalo_fim, 0, 5);
            }

            list($hrsSat, $minSat) = calcHorasTrabalhadas($iniSat, $intIniSat, $intFimSat, $fimSat);
            $totalSat = $hrsSat . ":" . $minSat;
        } elseif (str_contem($turno->dia_semana, '5') || str_contem($turno->dia_semana, '8')) {
            $iniSat = "";
            $intIniSat = "";
            $intFimSat = "";
            $fimSat = "";

            $sabado = $turno;

            if (validaHoraPonto(substr($sabado->hora_ini, 0, 5))) {
                $iniSat = substr($sabado->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->hora_fim, 0, 5))) {
                $fimSat = substr($sabado->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->intervalo_ini, 0, 5))) {
                $intIniSat = substr($sabado->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($sabado->intervalo_fim, 0, 5))) {
                $intFimSat = substr($sabado->intervalo_fim, 0, 5);
            }

            list($hrsSat, $minSat) = calcHorasTrabalhadas($iniSat, $intIniSat, $intFimSat, $fimSat);
            $totalSat = $hrsSat . ":" . $minSat;
        }

        if ($turno->domingo) {
            $domingo = (new Horas())->find(
                "id_turno = :id_turno AND dia_semana = :dia_semana",
                "id_turno={$turno->id}&dia_semana=6"
            )->fetch();
            $iniSun = "";
            $intIniSun = "";
            $intFimSun = "";
            $fimSun = "";
            if (validaHoraPonto(substr($domingo->hora_ini, 0, 5))) {
                $iniSun = substr($domingo->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->hora_fim, 0, 5))) {
                $fimSun = substr($domingo->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->intervalo_ini, 0, 5))) {
                $intIniSun = substr($domingo->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->intervalo_fim, 0, 5))) {
                $intFimSun = substr($domingo->intervalo_fim, 0, 5);
            }

            list($hrsSun, $minSun) = calcHorasTrabalhadas($iniSun, $intIniSun, $intFimSun, $fimSun);
            $totalSun = $hrsSun . ":" . $minSun;
        } elseif (str_contem($turno->dia_semana, '6')) {
            $iniSat = "";
            $intIniSat = "";
            $intFimSat = "";
            $fimSat = "";

            $domingo = $turno;

            if (validaHoraPonto(substr($domingo->hora_ini, 0, 5))) {
                $iniSat = substr($domingo->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->hora_fim, 0, 5))) {
                $fimSat = substr($domingo->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->intervalo_ini, 0, 5))) {
                $intIniSat = substr($domingo->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($domingo->intervalo_fim, 0, 5))) {
                $intFimSat = substr($domingo->intervalo_fim, 0, 5);
            }

            list($hrsSat, $minSat) = calcHorasTrabalhadas($iniSat, $intIniSat, $intFimSat, $fimSat);
            $totalSat = $hrsSat . ":" . $minSat;
        }

        $ponto2 = (new Ponto2())->find(
            "id_ponto1 = :id_ponto1",
            "id_ponto1={$id_ponto1}"
        )->order("dia asc")->fetch(true);

        $totalHours = 0;
        $totalMinutes = 0;
        foreach ($ponto2 as $hrDia) {
            $ini = "";
            $intIni = "";
            $intFim = "";
            $fim = "";
            if (validaHoraPonto(substr($hrDia->hora_ini, 0, 5))) {
                $ini = substr($hrDia->hora_ini, 0, 5);
            }
            if (validaHoraPonto(substr($hrDia->hora_fim, 0, 5))) {
                $fim = substr($hrDia->hora_fim, 0, 5);
            }
            if (validaHoraPonto(substr($hrDia->intervalo_ini, 0, 5))) {
                $intIni = substr($hrDia->intervalo_ini, 0, 5);
            }
            if (validaHoraPonto(substr($hrDia->intervalo_fim, 0, 5))) {
                $intFim = substr($hrDia->intervalo_fim, 0, 5);
            }

            list($hrs, $min) = calcHorasTrabalhadas($ini, $intIni, $intFim, $fim);

            $hrDia->total = $hrs . ":" . $min;

            $totalHours += $hrs;
            $totalMinutes += $min;

            if ($totalMinutes >= 60) {
                $totalHours += floor($totalMinutes / 60);
                $totalMinutes = $totalMinutes % 60;
            }

            $total = $totalHours . ":" . str_pad($totalMinutes, 2, "0", STR_PAD_LEFT);
        }

        $faltas = (new Faltas())->find()->fetch(true);

        $front = [
            "titulo" => "Cartões de Ponto - Taskforce",
            "user" => $this->user,
            "secTit" => "Revisão Cartão de Ponto"
        ];

        echo $this->view->render("tcsistemas.ponto/ponto/pontoFolhaEdit", [
            "func" => $func,
            "ponto1" => $ponto1,
            "ponto2" => $ponto2,
            "total" => $total,
            "turno" => $turno,
            "segunda" => $segunda,
            "terca" => $terca,
            "quarta" => $quarta,
            "quinta" => $quinta,
            "sexta" => $sexta,
            "sabado" => $sabado,
            "domingo" => $domingo,
            "totalTurno" => $totalTurno,
            "totalSeg" => $totalSeg,
            "totalTer" => $totalTer,
            "totalQua" => $totalQua,
            "totalQui" => $totalQui,
            "totalSex" => $totalSex,
            "totalSat" => $totalSat,
            "totalSun" => $totalSun,
            "faltas" => $faltas,
            "front" => $front
        ]);
    }

    public function salvar($data): void
    {

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $dados = json_decode($data['tabelaDados'], true);

        foreach ($dados as $key => $vlr) {
            if (array_key_exists('id_ponto1', $vlr)) {
                $ponto1 = (new Ponto1())->findById($vlr['id_ponto1']);
                $antes = clone $ponto1->data();
                $ponto1->total_horas = $vlr['total_ponto1'];
                $ponto1->banco_horas = $vlr['banco_ponto1'];
                $ponto1->extra_horas = $vlr['extras_ponto1'];
                $ponto1->status = 'R';
                $ponto1->id_users = $id_user;

                if (!$ponto1->save) {
                    $mensagem = $this->message->error("ERRO AO SALVAR CABEÇALHO")->render();
                    echo $mensagem;
                }

                $logPonto1 = new Log();
                $logPonto1->registrarLog("U", $ponto1->getEntity(), $ponto1->id, $antes, $ponto1->data());
            } else {
                $horaIni = (preg_match('/^\d{2}:\d{2}$/', $vlr['hora_ini'])) ? $vlr['hora_ini'] . ":00.0000000" : $vlr['hora_ini'];
                $horaFim = (preg_match('/^\d{2}:\d{2}$/', $vlr['hora_fim'])) ? $vlr['hora_fim'] . ":00.0000000" : $vlr['hora_fim'];
                $intIni = (preg_match('/^\d{2}:\d{2}$/', $vlr['intervalo_ini'])) ? $vlr['intervalo_ini'] . ":00.0000000" : $vlr['intervalo_ini'];
                $intFim = (preg_match('/^\d{2}:\d{2}$/', $vlr['intervalo_fim'])) ? $vlr['intervalo_fim'] . ":00.0000000" : $vlr['intervalo_fim'];

                $ponto2 = (new Ponto2())->findById($vlr['id_ponto2']);
                $antes = clone $ponto2->data();
                $ponto2->id_users = $id_user;

                $obs = isset($vlr['obs']) ? ll_decode($vlr['obs']) : 0;

                $ponto2->hora_ini = $horaIni;
                $ponto2->hora_fim = $horaFim;
                $ponto2->intervalo_ini = $intIni;
                $ponto2->intervalo_fim = $intFim;
                $ponto2->obs = ll_intValida($obs) ? $obs : null;
                $ponto2->checkbox = $vlr['checkbox'];
                $ponto2->total_horas = $vlr['total_ponto2'];
                $ponto2->banco_horas = $vlr['banco_ponto2'];
                $ponto2->extra_horas = $vlr['extras_ponto2'];

                if (!$ponto2->save) {
                    $mensagem = $this->message->error("ERRO AO SALVAR DADOS DO DIA {$ponto2->dia}")->render();
                    echo $mensagem;
                    return;
                }

                $logPonto2 = new Log();
                $logPonto2->registrarLog("U", $ponto2->getEntity(), $ponto2->id, $antes, $ponto2->data());
            }
        }
        $mensagem = $this->message->success("ALTERAÇÕES SALVAS!");
        echo $mensagem;
    }

    public function gerarPdf($data)
    {

        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $id_ponto1 = ll_decode($data['id_ponto1']);
        $ponto1 = (new Ponto1())->findById($id_ponto1);

        $emp = (new Emp2())->findById($id_empresa);
        $func = (new Ent())->findById($ponto1->id_func);
        $turno = (new Turno())->findById($ponto1->id_turno);
        $entfun = (new EntFun())->findByIdEnt($ponto1->id_func);

        $logo = "";
        if (!empty($emp->logo)) {
            $logo = '<span>
                <img class="thumb" src="' . CONF_FILES_URL . $emp->logo . '">
            </span>';
        }

        $horasExtras = $ponto1->extra_horas;

        $ponto2 = (new Ponto2())->find(
            "id_ponto1 = :id_ponto1",
            "id_ponto1={$id_ponto1}"
        )->order("dia asc")->fetch(true);

        $faltas = (new Faltas())->find()->fetch(true);

        $imagem = "";
        if (!is_null($emp->imagem)) {
            $imagem = 'data:' . $emp->tipo_imagem . ';base64,' . $emp->imagem;
        }

        echo $this->view->render("tcsistemas.ponto/pdf/pdf", [
            "titulo" => "PDF",
            "user" => $this->user,
            "func" => $func,
            "ponto1" => $ponto1,
            "ponto2" => $ponto2,
            "turno" => $turno,
            "emp" => $emp,
            "faltas" => $faltas,
            "imagem" => $imagem,
            "extras" => $horasExtras,
            "entfun" => $entfun,
            "logo" => $logo
        ]);
    }

    public function verificar($data): void
    {
        $id_empresa = $this->user->id_emp2;
        $mes = $data['mes'];
        $ano = $data['ano'];

        $funcionarios = (new Ent())->find(
            "tipo = :tipo",
            "tipo=3"
        )->fetch(true);

        $response = [];

        if ($funcionarios) {
            foreach ($funcionarios as $funcionario) {
                $hasGeneratedPonto = (new Ponto1())->find(
                    "id_func = :id_func AND mes = :mes AND ano = :ano",
                    "id_func={$funcionario->id}&mes={$mes}&ano={$ano}",
                    "*",
                    false
                )->fetch();

                $response[$funcionario->id] = $hasGeneratedPonto ? true : false;
            }
        }

        echo json_encode($response);
    }

    public function filter(array $data): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if (empty($data['mes']) || empty($data['ano'])) {
            $json = $this->message->warning("Selecione um período")->render();
            echo $json;
            return;
        }

        $pontos = (new Ponto1())->find(
            "mes = :mes AND ano = :ano",
            "mes={$data['mes']}&ano={$data['ano']}"
        )->fetch(true);

        if (is_null($pontos) || $pontos == "") {
            $json = $this->message->warning("SEM CARTÕES GERADOS PARA A COMPETÊNCIA {$data['mes']}/{$data['ano']}")->render();
            echo $json;
            return;
        }

        $mes = ll_encode($data['mes']);
        $ano = ll_encode($data['ano']);

        $json = url("ponto/folhas/{$mes}/{$ano}");
        echo $json;
    }

    public function excluir($data)
    {

        $id = ll_decode($data['id']);

        $ponto2 = (new Ponto2())->find(
            "id_ponto1 = :id_ponto1",
            "id_ponto1={$id}",
            "*",
            false
        )->fetch(true);

        foreach ($ponto2 as $p2) {
            $exc = (new Ponto2())->findById($p2->id);
            $exc->destroy();
        }

        $ponto1 = (new Ponto1())->findById($id);
        $antes = clone $ponto1->data();

        if (!$ponto1->destroy()) {
            $json['message'] = $this->message->error("ERRO AO DELETAR PONTO1")->render();
            echo json_encode($json);
            return;
        }

        $logPonto1 = new Log();
        $logPonto1->registrarLog("D", $ponto1->getEntity(), $ponto1->id, $antes, null);

        $json['message'] = $this->message->success("REGISTRO EXCLUÍDO")->render();
        $json['reload'] = true;
        echo json_encode($json);
    }

    public function feriados($data): void
    {
        $id_user = $this->user->id;
        $id_emp = $this->user->id_emp2;

        $true = true;

        $mes = date("m");
        $ano = date("Y");
        $feriadosBanco = new Feriados;
        if ($id_emp == 1) {
            $dias = $feriadosBanco->find(
                "(id_users = :id_users OR id < :id) AND (ano = :ano OR recorrente = :recorrente)",
                "id_users={$id_user}&id=9&ano={$ano}&recorrente={$true}",
                "*",
                false
            )->fetch(true);
        } else {
            $dias = $feriadosBanco->find(
                "(id_emp2 = :id_emp2 OR id < :id) AND (ano = :ano OR recorrente = :recorrente)",
                "id_emp2={$id_emp}&id=9&ano={$ano}&recorrente={$true}",
                "*",
                false
            )->fetch(true);
        }

        $feriadosResult = retornaFeriados($ano, $dias);
        ksort($feriadosResult);

        $front = [
            "titulo" => "Cartões de Ponto / Feriados - Taskforce",
            "user" => $this->user,
            "secTit" => "Feriados"
        ];

        echo $this->view->render("tcsistemas.ponto/ponto/feriadosForm", [
            "feriadosResult" => $feriadosResult,
            "front" => $front
        ]);
    }

    public function novo($data)
    {

        $id_user = $this->user->id;
        $id_emp = $this->user->id_emp2;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            if (!filter_var($data['descricao'], FILTER_SANITIZE_SPECIAL_CHARS)) {
                $json['message'] = $this->message->error("ERRO! Caracteres inválidos para o campo 'FERIADO'. Tente novamente!")->render();
                echo json_encode($json);
                return;
            }

            if (!validate_date($data['dias'])) {
                $json['message'] = $this->message->warning("Data inválida.")->render();
                echo json_encode($json);
                return;
            };

            $ano = explode("-", $data['dias']);
            $dias = $data['dias'];

            $verificaDia = (new Feriados)->find(
                "dias = :dias",
                "dias={$dias}"
            )->fetch();

            if (!is_null($verificaDia)) {
                $json['message'] = $this->message->warning("Já existe feriado pro dia selecionado.")->render();
                echo json_encode($json);
                return;
            }

            $feriado = new Feriados;

            $feriado->id_emp2 = $id_emp;
            $feriado->descricao = $data['descricao'];
            $feriado->dias = $data['dias'];
            $feriado->recorrente = (isset($data['recorrente']) && $data['recorrente'] === 'on') ? 1 : 0;
            $feriado->ano = (isset($data['recorrente']) && $data['recorrente'] === 'on') ? null : $ano[0];
            $feriado->padrao = 0;
            $feriado->id_users = $id_user;

            if (!$feriado->save()) {
                $json['message'] = $this->message->warning("Não foi possível cadastrar.")->render();
                echo json_encode($json);
                return;
            }

            $logFeriado = new Log();
            $logFeriado->registrarLog("C", $feriado->getEntity(), $feriado->id, null, $feriado->data());

            $json['message'] = $this->message->success("Feriado registrado")->flash();
            $json['redirect'] = url('ponto/feriados');
            echo json_encode($json);
        }
    }

    public function excluirFeriado($data)
    {
        $id_user = $this->user->id;
        $id_emp = $this->user->id_emp2;

        $feriado = (new Feriados)->findById(ll_decode($data['id']));
        $antes = clone $feriado->data();

        $feriado->destroy();

        $log = new Log();
        $log->registrarLog("D", $feriado->getEntity(), $feriado->id, $antes, null);

        $json['message'] = $this->message->success("Feriado excluído com sucesso!")->flash();
        $json['redirect'] = url('ponto/feriados');
        echo json_encode($json);
    }


    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
