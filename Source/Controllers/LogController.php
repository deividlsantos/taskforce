<?php

namespace Source\Controllers;

use League\Plates\Engine;
use Source\Boot\Message;
use Source\Models\Auth;
use Source\Models\Emp2;
use Source\Models\Ent;
use Source\Models\Log;
use Source\Models\Users;

class LogController
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
    {;
        $empresas = (new Emp2())->find(null, null, 'id, razao', false)->fetch(true);
        $logTabelas = (new Log())->find(null, null, 'tabela', false)->fetch(true);
        $tabelas = array_map(fn($logTabelas) => $logTabelas->tabela, $logTabelas);
        $TabelasUnicas = array_unique($tabelas);

        echo $this->view->render("tcsistemas.log/log", [
            "empresas" => $empresas,
            "tabelas" => $TabelasUnicas
        ]);
    }

    public function selectUsers($data): void
    {
        if (!empty($data['id']) && is_array($data['id'])) {
            $empresas_id = implode(", ", array_map('intval', $data['id'])); // sanitiza como int
            $usuarios = (new Users())->find("id_emp2 IN (:ids)", "ids={$empresas_id}", "id, nome", false)->order('nome')->fetch(true);
        } elseif (!empty($data['id'])) {
            $empresa_id = (int) $data['id'];
            $usuarios = (new Users())->find("id_emp2 = :id", "id={$empresa_id}", "id, nome", false)->order('nome')->fetch(true);
        } else {
            $usuarios = (new Users())->find(null, null, "id, nome", false)->order('nome')->fetch(true);
        }

        if ($usuarios) {
            // Transforma os objetos em arrays associativos
            echo json_encode(array_map(fn($user) => $user->data(), $usuarios));
        } else {
            echo json_encode([]);
        }
    }

    public function pesquisaLogs($data): void
    {
        $tabelaUser = (new Users())->getEntity();
        $tabelaEmp2 = (new Emp2())->getEntity();
        // LEFT JOIN com users e emp2
        if (!empty($data['id_emp2']) && is_array(($data['id_emp2']))) {
            $empresas_id  = implode(", ", array_map('intval', $data['id_emp2'])); // sanitiza como int

           
        } elseif (!empty($data['id_emp2'])) {
            $emp2 = (int)$data['id_emp2'];
            $logs = (new Log())->find("log.id_emp2 = :id", "id={$emp2}", "log.*", false)->fetch(true);
        } else {
            $logs = (new Log())->find(null, null, "log.*, {$tabelaEmp2}.razao empresa_razao, {$tabelaUser}.nome usuario_nome", false)->join($tabelaEmp2, "{$tabelaEmp2}.id = log.id_emp2", "LEFT")->join($tabelaUser, "{$tabelaUser}.id = log.id_users", "LEFT")->fetch(true);
        }


        echo json_encode(array_map(fn($logs) => $logs->data(), $logs ?? []));
    }

    
}
