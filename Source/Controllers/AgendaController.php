<?php

namespace Source\Controllers;

use DateTime;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Materiais;
use Source\Models\Obras;
use Source\Models\Os1;
use Source\Models\Os2;
use Source\Models\Os3;
use Source\Models\Servico;
use Source\Models\Status;

class AgendaController extends Controller
{

    public function __construct()
    {
        parent::__construct();

        //** TELA DE AGENDA TEMPORARIAMENTE SUSPENSA */
        redirect("dash");

        if ($this->user->id_emp2 != 1 && $this->user->os != "X") {
            $this->message->error("Você não tem permissão para acessar essa página")->flash();
            redirect("dash");
        }
    }

    public function index(): void
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        $ordens = "";
        $secTit = "Cadastrar";

        if (isset($data['id_ordens'])) {
            $id = ll_decode($data['id_ordens']);
            $ordens = (new Os1())->findById($id);
            $secTit = "Visualizar/Editar";
        }

        $os1 = (new Os1())->find()->fetch(true);

        $os2 = (new Os2())->find()->order("dataexec ASC, horaexec ASC")->fetch(true);

        $os3 = (new Os3())->find("id_os2 IS NULL")->fetch(true);

        $os2os3 = (new Os3())->find("id_os2 IS NOT NULL")->order("id_os2 desc")->fetch(true);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $operador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=3&status=A"
        )->fetch(true);

        $status = (new Status())->find(
            "id_emp2 = :id_emp2",
            "id_emp2=1",
            "*",
            false
        )->fetch(true);

        $servico = (new Servico())->find()->fetch(true);

        $material = (new Materiais())->find()->fetch(true);

        $obras = (new Obras())->find()->fetch(true);


        if ($os1) {
            foreach ($os1 as $os) {
                foreach ($status as $st) {
                    if ($os->id_status == $st->id) {
                        $os->cor = $st->cor;
                        $os->status = $st->descricao;
                    }
                }
                foreach ($cliente as $cl) {
                    if ($os->id_cli == $cl->id) {
                        $os->cliente = $cl->nome;
                    }
                }
            }
        }

        if (!empty($os2)) {
            foreach ($os2 as $os) {
                foreach ($servico as $sv) {
                    if ($os->id_servico == $sv->id) {
                        $os->servico = $sv->nome;
                    }
                }

                foreach ($operador as $cl) {
                    if ($os->id_colaborador == $cl->id) {
                        $os->colaborador = $cl->nome;
                    }
                }

                if ($os->status == "A") {
                    $os->cor = (new Status())->findById(2, 'cor')->cor;
                } else if ($os->status == "I") {
                    $os->cor = (new Status())->findById(3, 'cor')->cor;
                } else if ($os->status == "P") {
                    $os->cor = (new Status())->findById(4, 'cor')->cor;
                } else if ($os->status == "C") {
                    $os->cor = (new Status())->findById(5, 'cor')->cor;
                } else if ($os->status == "D") {
                    $os->cor = (new Status())->findById(7, 'cor')->cor;
                }
            }
        }


        $dataOs1 = $os1 ? objectsToArray($os1) : "";
        $dataOs2 = $os2 ? objectsToArray($os2) : "";
        $dataOs3 = $os3 ? objectsToArray($os3) : "";
        $dataOs2Os3 = $os2os3 ? objectsToArray($os2os3) : "";

        $front = [
            "titulo" => "Agenda - Taskforce",
            "user" => $this->user,
            "secTit" => "Agenda"
        ];

        echo $this->view->render("tcsistemas.os/agenda/agenda", [
            "front" => $front,
            "ordens" => $ordens,
            "cliente" => $cliente,
            "operador" => $operador,
            "status" => $status,
            "servico" => $servico,
            "material" => $material,
            "dataos1" => $dataOs1,
            "dataos2" => $dataOs2,
            "dataos3" => $dataOs3,
            "dataos2os3" => $dataOs2Os3,
            "obras" => $obras
        ]);
    }

    public function refreshAgenda($data)
    {
        $id_user = $this->user->id;
        $id_empresa = $this->user->id_emp2;

        if ("POST" == $_SERVER['REQUEST_METHOD']) {

            $os2Items = (new Os2())->findByIdOs($data['id']);

            $tabela = $os2Items[0]->getEntity();

            // Verifica se há itens `os2` para calcular a diferença
            if (count($os2Items) > 0) {
                // Converte a data original do primeiro item e a nova data em objetos DateTime
                $originalDate = new DateTime($os2Items[0]->dataexec);
                $newDate = new DateTime($data['newDate']);

                // Calcula a diferença em dias
                $interval = $originalDate->diff($newDate)->days;
                $interval = $newDate > $originalDate ? $interval : -$interval; // Ajusta se a data for anterior

                // Atualiza cada item `os2` com a data proporcional
                foreach ($os2Items as $index => $os) {

                    $antes = clone $os->data();
                    $itemDate = new DateTime($os->dataexec);
                    $itemDate->modify("{$interval} days"); // Adiciona a diferença de dias

                    $novaDataExec = $itemDate->format('Y-m-d');

                    $hoje = new DateTime();

                    if ($novaDataExec < $hoje->format('Y-m-d')) {
                        $json['message'] = $this->message->error("A data de execução não pode ser menor que a data atual.")->render();
                        echo json_encode($json);
                        return;
                    }


                    // $tempoTotal = $os->tempo;
                    // $novoHorario = (new OsController())->calculaHorarioExecucao(
                    //     $os->id_emp2,
                    //     $os->id_colaborador,
                    //     $novaDataExec,
                    //     $tempoTotal,
                    //     28800, // Hora de início
                    //     64800, // Hora de fim
                    //     $os->horaexec
                    // );

                    $os->dataexec = $novaDataExec;
                    $os->id_users = $id_user;
                    // $os->horaexec = $novoHorario['horaexec'];
                    // $os->horafim = $novoHorario['horafim'];
                    $os->save(); // Chama o método de salvamento

                    $log = new Log();
                    $log->registrarLog("U", $tabela, $os->id, $antes, $os->data());
                }
            }
            return;
        }


        $os1 = (new Os1())->find()->fetch(true);

        $os2 = (new Os2())->find()->order("dataexec ASC, horaexec ASC")->fetch(true);

        $os3 = (new Os3())->find("id_os2 IS NULL")->fetch(true);

        $os2os3 = (new Os3())->find("id_os2 IS NOT NULL")->order("id_os2 desc")->fetch(true);

        $status = (new Status())->find(
            "id_emp2 = :id_emp2",
            "id_emp2=1",
            "*",
            false
        )->fetch(true);

        $cliente = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=1&status=A"
        )->fetch(true);

        $servico = (new Servico())->find()->fetch(true);

        $colaborador = (new Ent())->find(
            "tipo = :tipo AND status = :status",
            "tipo=3&status=A"
        )->fetch(true);

        foreach ($os1 as $os) {
            foreach ($status as $st) {
                if ($os->id_status == $st->id) {
                    $os->cor = $st->cor;
                    $os->status = $st->descricao;
                }
            }

            foreach ($cliente as $cl) {
                if ($os->id_cli == $cl->id) {
                    $os->cliente = $cl->nome;
                }
            }
        }

        foreach ($os2 as $os) {
            foreach ($servico as $sv) {
                if ($os->id_servico == $sv->id) {
                    $os->servico = $sv->nome;
                }
            }

            foreach ($colaborador as $cl) {
                if ($os->id_colaborador == $cl->id) {
                    $os->colaborador = $cl->nome;
                }
            }

            if ($os->status == "A") {
                $os->cor = (new Status())->findById(2, 'cor')->cor;
            } else if ($os->status == "I") {
                $os->cor = (new Status())->findById(3, 'cor')->cor;
            } else if ($os->status == "P") {
                $os->cor = (new Status())->findById(4, 'cor')->cor;
            } else if ($os->status == "C") {
                $os->cor = (new Status())->findById(5, 'cor')->cor;
            } else if ($os->status == "D") {
                $os->cor = (new Status())->findById(7, 'cor')->cor;
            }
        }

        $dataOs1 = objectsToArray($os1);
        $dataOs2 = objectsToArray($os2);
        $dataOs3 = "";
        if ($os3) {
            $dataOs3 = objectsToArray($os3);
        }
        $dataOs2Os3 = "";
        if ($os2os3) {
            $dataOs2Os3 = objectsToArray($os2os3);
        }

        $json["os1"] = $dataOs1;
        $json["os2"] = $dataOs2;
        $json["os3"] = $dataOs3;
        $json["os2os3"] = $dataOs2Os3;

        echo json_encode($json);
    }

    public function error(array $data): void
    {
        echo "<h1>Erro {$data['errcode']}</h1>";
    }
}
