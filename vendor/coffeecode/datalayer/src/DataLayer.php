<?php

namespace CoffeeCode\DataLayer;

use Exception;
use PDO;
use PDOException;
use stdClass;

/**
 * Class DataLayer
 * @package CoffeeCode\DataLayer
 */
abstract class DataLayer
{
    use CrudTrait;

    /** @var string $entity database table */
    private $entity;

    /** @var string $primary table primary key field */
    private $primary;

    /** @var array $required table required fields */
    private $required;

    /** @var string $timestamps control created and updated at */
    private $timestamps;

    /** @var string */
    protected $statement;

    /** @var string */
    protected $params;

    /** @var string */
    protected $group;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

    /** @var \PDOException|null */
    protected $fail;

    /** @var object|null */
    protected $data;

    /** @var int */
    protected $idEmpresa;

    /** @var int */
    protected $idUsuario;

    /** @var string */
    protected $joins = "";

    /**
     * DataLayer constructor.
     * @param string $entity
     * @param array $required
     * @param string $primary
     * @param bool $timestamps
     */
    public function __construct(string $entity, array $required, string $primary = 'id', bool $timestamps = true)
    {
        $this->entity = $entity;
        $this->primary = $primary;
        $this->required = $required;
        $this->timestamps = $timestamps;
    }

    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        if (empty($this->data)) {
            $this->data = new stdClass();
        }

        $this->data->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->data->$name);
    }

    /**
     * @param $name
     * @return string|null
     */
    public function __get($name)
    {
        $method = $this->toCamelCase($name);
        if (method_exists($this, $method)) {
            return $this->$method();
        }

        if (method_exists($this, $name)) {
            return $this->$name();
        }

        return ($this->data->$name ?? null);
    }

    /*
     * @return PDO mode
     */
    public function columns($mode = PDO::FETCH_OBJ)
    {
        $stmt = Connect::getInstance()->prepare("DESCRIBE {$this->entity}");
        $stmt->execute($this->params);
        return $stmt->fetchAll($mode);
    }


    /**
     * @return object|null
     */
    public function data(): ?object
    {
        return $this->data;
    }

    /**
     * @return PDOException|Exception|null
     */
    public function fail()
    {
        return $this->fail;
    }

    /**
     * @return void
     */
    protected function getFiltro()
    {
        return $this->idEmpresa == 1 ? "id_users = :id_users" : "id_emp2 = :id_emp2";
    }

    /**
     * @return void
     */
    protected function getBind()
    {
        return $this->idEmpresa == 1 ? "id_users={$this->idUsuario}" : "id_emp2={$this->idEmpresa}";
    }

    /**
     * @param string|null $terms
     * @param string|null $params
     * @param string $columns
     * @return DataLayer
     */
    public function find(?string $terms = null, ?string $params = null, string $columns = "*", bool $applyCompanyFilter = true): DataLayer
    {
        // Pegar usuário e empresa da sessão
        $idUsuario = $_SESSION["authUser"] ?? null;
        $idEmpresa = $_SESSION["authEmp"] ?? null;

        // Aplicar filtro de empresa se necessário
        if ($applyCompanyFilter && $idEmpresa) {
            if ($idEmpresa == 1) {
                // Se a empresa for 1, filtra pelos registros do usuário
                $terms = $terms ? "{$terms} AND id_users = :id_users" : "id_users = :id_users";
                $params = $params ? "{$params}&id_users={$idUsuario}" : "id_users={$idUsuario}";
            } else {
                // Senão, filtra normalmente pela empresa
                $terms = $terms ? "{$terms} AND id_emp2 = :id_emp2" : "id_emp2 = :id_emp2";
                $params = $params ? "{$params}&id_emp2={$idEmpresa}" : "id_emp2={$idEmpresa}";
            }
        }

        // Inclui os JOINs na consulta
        $this->statement = "SELECT {$columns} FROM {$this->entity}{$this->joins}";
        $this->statement .= $terms ? " WHERE {$terms}" : "";

        if (is_string($params)) {
            parse_str($params, $this->params);
        } else {
            $this->params = $params;
        }

        return $this;
    }

    /**
     * @param string $table
     * @param string $condition
     * @param string $type
     * @return DataLayer
     */
    public function join(string $table, string $condition, string $type = "INNER"): DataLayer
    {
        $this->joins .= " {$type} JOIN {$table} ON {$condition}";
        return $this;
    }

    /**
     * @param int $id
     * @param string $columns
     * @return DataLayer|null
     */
    public function findById(int $id, string $columns = "*"): ?DataLayer
    {
        return $this->find("{$this->primary} = :id", "id={$id}", $columns, false)->fetch();
    }

    /**
     * @param string $column
     * @return DataLayer|null
     */
    public function group(string $column): ?DataLayer
    {
        $this->group = " GROUP BY {$column}";
        return $this;
    }

    /**
     * @param string $columnOrder
     * @return DataLayer|null
     */
    public function order(string $columnOrder): ?DataLayer
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

    /**
     * @param int $limit
     * @return DataLayer|null
     */
    public function limit(int $limit): ?DataLayer
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return DataLayer|null
     */
    public function offset(int $offset): ?DataLayer
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

    /**
     * @param bool $all
     * @return array|mixed|null
     */
    public function fetch(bool $all = false)
    {
        try {
            $stmt = Connect::getInstance()->prepare($this->statement . $this->group . $this->order . $this->limit . $this->offset);
            $stmt->execute($this->params);

            if (!$stmt->rowCount()) {
                return null;
            }

            if ($all) {
                return $stmt->fetchAll(PDO::FETCH_CLASS, static::class);
            }

            return $stmt->fetchObject(static::class);
        } catch (PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $stmt = Connect::getInstance()->prepare($this->statement);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        $primary = $this->primary;
        $id = null;

        try {
            if (!$this->required()) {
                throw new Exception("Preencha os campos necessários");
            }

            /** Update */
            if (!empty($this->data->$primary)) {
                $id = $this->data->$primary;
                $this->update($this->safe(), "{$this->primary} = :id", "id={$id}");
            }

            /** Create */
            if (empty($this->data->$primary)) {
                $id = $this->create($this->safe());
            }

            if (!$id) {
                return false;
            }

            $this->data = $this->findById($id)->data();
            return true;
        } catch (Exception $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /**     
     * @return string
     */
    public function getEntity(): string
    {
        return $this->entity;
    }

    /**
     * Inicia uma transação
     * 
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return Connect::getInstance()->beginTransaction();
    }

    /**
     * Confirma a transação
     * 
     * @return bool
     */
    public function commit(): bool
    {
        return Connect::getInstance()->commit();
    }

    /**
     * Desfaz a transação
     * 
     * @return bool
     */
    public function rollBack(): bool
    {
        return Connect::getInstance()->rollBack();
    }

    /**
     * @return bool
     */
    public function destroy(): bool
    {
        $primary = $this->primary;
        $id = $this->data->$primary;

        if (empty($id)) {
            return false;
        }

        return $this->delete("{$this->primary} = :id", "id={$id}");
    }

    /**
     * @return bool
     */
    protected function required(): bool
    {
        $data = (array) $this->data();
        foreach ($this->required as $field) {
            if (empty($data[$field])) {
                if (!is_int($data[$field])) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return array|null
     */
    protected function safe(): ?array
    {
        $safe = (array) $this->data;
        unset($safe[$this->primary]);
        return $safe;
    }


    /**
     * @param string $string
     * @return string
     */
    protected function toCamelCase(string $string): string
    {
        $camelCase = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        $camelCase[0] = strtolower($camelCase[0]);
        return $camelCase;
    }
}
