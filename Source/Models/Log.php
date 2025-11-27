<?php

namespace Source\Models;

use CoffeeCode\DataLayer\DataLayer;

class Log extends DataLayer
{

    protected $user;

    public function __construct()
    {
        parent::__construct("log", ["id_emp2", "id_users"], "id", false);
    }

    /**
     * Registra um log de alteração no banco de dados
     * 
     * @param string $acao C = CREATE | U = UPDATE | D = DELETE
     * @param string $tabela Nome da tabela afetada
     * @param integer|null $id_registro ID do registro alterado
     * @param [type] $antes Valores antes da alteração (para UPDATE)
     * @param [type] $depois Valores depois da alteração (para UPDATE)
     * @return Log
     */
    public function registrarLog(string $acao, string $tabela, $id_registro = null, $antes = null, $depois = null, $camposExcluir = []): Log
    {
        if (empty($acao) || empty($tabela)) {
            return $this;
        }

        // Se for UPDATE, verifica se houve alteração
        if ($acao == "U") {
            if (empty($antes) && empty($depois)) {
                return $this;
            }

            if ($antes == $depois) {
                return $this;
            }

            if (empty($id_registro)) {
                return $this;
            }

            $antes = $this->filtrarCampos($antes, $camposExcluir);
        }

        if ($acao != "D") {
            $depois = $this->filtrarCampos($depois, $camposExcluir);
        } else {
            $antes = $this->filtrarCampos($antes, $camposExcluir);
        }

        if ($antes == $depois) {
            return $this;
        }

        if ($acao == "U") {
            $acao = "UPDATE";
        } elseif ($acao == "C") {
            $acao = "CREATE";
        } elseif ($acao == "D") {
            $acao = "DELETE";
        }

        $user = Auth::user();
        $ip = $_SERVER["REMOTE_ADDR"] ?? "UNKNOWN";
        $userAgent = $_SERVER["HTTP_USER_AGENT"] ?? "UNKNOWN";

        $this->id_emp2 = $user->id_emp2;
        $this->id_users = $user->id;
        $this->acao = $acao;
        $this->tabela = $tabela;
        $this->id_registro = $id_registro;
        $this->valores_antes = $antes;
        $this->valores_depois = $depois;
        $this->ip = $ip;
        $this->user_agent = $userAgent;
        $this->save();

        return $this;
    }


    /**
     *
     * @param object $dados
     * @param [type] $camposIncluir
     * @param [type] $camposExcluir
     * @return string|null
     */
    private function filtrarCampos(object $dados, array $camposExcluir = []): ?string
    {
        $camposExcluir[] = "id";
        $camposExcluir[] = "id_emp2";
        $camposExcluir[] = "id_users";
        $camposExcluir[] = "created_at";
        $camposExcluir[] = "updated_at";

        if (empty($dados)) {
            return null;
        }

        if (is_object($dados)) {
            $dados = (array) $dados;
        }

        $dadosFiltrados = [];

        foreach ($dados as $key => $value) {
            if (!empty($camposExcluir) && in_array($key, $camposExcluir)) {
                continue;
            }

            $dadosFiltrados[] = "$key|$value";
        }

        return implode("; ", $dadosFiltrados);
    }

    public function loadLogs(
        $entity,
        $id = [],
        $acao = [],
        $usuario = [],
        $tabela = [],
        $inicial = 0,
        $final = 0,
        $offset = 0,
        $limit = 50
    ) {
        $user = new Users();
        $emp = new Emp2();
        $conn = Database::getInstance();

        $sql = "SELECT l.*, 
               u.nome AS usuario_nome, 
               e.razao AS empresa_razao
        FROM {$entity} AS l
        LEFT JOIN {$user->getEntity()} AS u ON l.id_users = u.id
        LEFT JOIN {$emp->getEntity()} AS e ON l.id_emp2 = e.id";

        $conditions = [];
        $params = [];

        // Normalização de arrays
        $normalize = function ($value) {
            if ($value === null || $value === '' || (is_array($value) && empty($value))) {
                return [];
            }
            return is_array($value) ? $value : [$value];
        };

        $id = $normalize($id);
        $acao = $normalize($acao);
        $tabela = $normalize($tabela);
        $usuario = $normalize($usuario);

        if (!empty($id)) {
            $placeholders = implode(',', array_fill(0, count($id), '?'));
            $conditions[] = "l.id_emp2 IN ($placeholders)";
            $params = array_merge($params, $id);
        }

        if (!empty($acao)) {
            $placeholders = implode(',', array_fill(0, count($acao), '?'));
            $conditions[] = "l.acao IN ($placeholders)";
            $params = array_merge($params, $acao);
        }

        if (!empty($tabela)) {
            $placeholders = implode(',', array_fill(0, count($tabela), '?'));
            $conditions[] = "l.tabela IN ($placeholders)";
            $params = array_merge($params, $tabela);
        }

        if (!empty($usuario)) {
            $placeholders = implode(',', array_fill(0, count($usuario), '?'));
            $conditions[] = "l.id_users IN ($placeholders)";
            $params = array_merge($params, $usuario);
        }

        if (!empty($inicial) && !empty($final)) {
            $conditions[] = "DATE(l.data_hora) BETWEEN ? AND ?";
            $params[] = $inicial;
            $params[] = $final;
        } elseif (!empty($inicial)) {
            $conditions[] = "DATE(l.data_hora) = ?";
            $params[] = $inicial;
        } elseif (!empty($final)) {
            $conditions[] = "DATE(l.data_hora) = ?";
            $params[] = $final;
        }

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        // 🔹 Mantém apenas parâmetros posicionais (sem :limit nem :offset)
        $sql .= " ORDER BY l.id DESC LIMIT ? OFFSET ?";

        $params[] = (int)$limit;
        $params[] = (int)$offset;

        $stmt = $conn->prepare($sql);

        // Faz o bind manual com tipos adequados
        foreach ($params as $index => $value) {
            $type = is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($index + 1, $value, $type);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function loadAcao($id, $status)
    {
        $conn = Database::getInstance();

        $sql = "SELECT valores_antes, valores_depois 
        FROM log WHERE id = :id AND acao = :status";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
