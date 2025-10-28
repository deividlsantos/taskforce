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
}
