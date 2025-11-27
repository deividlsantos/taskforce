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
        $logModel = new Log();

        $normalize = function ($value) {
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                return [];
            }
            return is_array($value) ? $value : [$value];
        };

        $id_emp2 = $normalize($data['id_emp2'] ?? []);
        $acao = $normalize($data['acao'] ?? []);
        $usuario = $normalize($data['usuario'] ?? []);
        $tabela = $normalize($data['campo'] ?? []);
        $inicial = $data['inicial'] ?? 0;
        $final = $data['final'] ?? 0;
        $offset = isset($data['offset']) ? (int)$data['offset'] : 0;
        $limit  = isset($data['limit']) ? (int)$data['limit'] : 50;

        $logs = $logModel->loadLogs(
            $logModel->getEntity(),
            $id_emp2,
            $acao,
            $usuario,
            $tabela,
            $inicial,
            $final,
            $offset,
            $limit
        );

        echo json_encode($logs);
    }


    public function logAcao($data)
    {

        $logModel = new Log();

        $logs = $logModel->loadAcao($data['id'], $data['status']);

        $antes = parseCampos($logs['valores_antes'] ?? '');
        $depois = parseCampos($logs['valores_depois'] ?? '');

        $campos = array_unique(array_merge(array_keys($antes), array_keys($depois)));

        $comparacao = [];
        foreach ($campos as $campo) {
            $valorAntes = $antes[$campo] ?? null;
            $valorDepois = $depois[$campo] ?? null;

            $comparacao[$campo] = [
                "antes" => $valorAntes,
                "depois" => $valorDepois,
                "alterado" => $valorAntes !== $valorDepois
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($comparacao);
    }
}
