<?php

namespace Models;

use Config\Database;
use PDO;
use PDOException;

abstract class BaseModel
{
    protected ?PDO $db;
    protected string $table = '';
    protected string $primaryKey = 'id';

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    /**
     * Get all records from table
     *
     * @param string $orderBy
     * @param string $direction
     * @return array
     */
    public function all(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        if (!$this->db) return [];
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` ORDER BY `{$orderBy}` {$direction}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find single record by primary key ID
     *
     * @param int $id
     * @return array|null
     */
    public function find(int $id): ?array
    {
        if (!$this->db) return null;
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Find records by single column criteria
     *
     * @param string $column
     * @param mixed $value
     * @return array
     */
    public function where(string $column, mixed $value): array
    {
        if (!$this->db) return [];
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :value");
        $stmt->execute(['value' => $value]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find single record by single column criteria
     *
     * @param string $column
     * @param mixed $value
     * @return array|null
     */
    public function findBy(string $column, mixed $value): ?array
    {
        if (!$this->db) return null;
        $stmt = $this->db->prepare("SELECT * FROM `{$this->table}` WHERE `{$column}` = :value LIMIT 1");
        $stmt->execute(['value' => $value]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    /**
     * Insert record into table
     *
     * @param array $data
     * @return int|bool Returns lastInsertId or false on failure
     */
    public function create(array $data): int|bool
    {
        if (!$this->db || empty($data)) return false;

        $columns = implode('`, `', array_keys($data));
        $placeholders = implode(', :', array_keys($data));

        $sql = "INSERT INTO `{$this->table}` (`{$columns}`) VALUES (:{$placeholders})";
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($data)) {
            return (int) $this->db->lastInsertId();
        }

        return false;
    }

    /**
     * Update record by ID
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        if (!$this->db || empty($data)) return false;

        $setParts = [];
        foreach (array_keys($data) as $key) {
            $setParts[] = "`{$key}` = :{$key}";
        }
        $setString = implode(', ', $setParts);

        $data['id_param'] = $id;

        $sql = "UPDATE `{$this->table}` SET {$setString} WHERE `{$this->primaryKey}` = :id_param";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute($data);
    }

    /**
     * Delete record by ID
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("DELETE FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Relationship Helper: Belongs To (Many to One)
     *
     * @param string $modelClass Model target class
     * @param string $foreignKey
     * @param int|null $foreignKeyValue
     * @return array|null
     */
    protected function belongsTo(string $modelClass, string $foreignKey, ?int $foreignKeyValue): ?array
    {
        if (!$foreignKeyValue) return null;
        $targetModel = new $modelClass();
        return $targetModel->find($foreignKeyValue);
    }

    /**
     * Relationship Helper: Has Many (One to Many)
     *
     * @param string $modelClass Model target class
     * @param string $foreignKey
     * @param int $localKeyValue
     * @return array
     */
    protected function hasMany(string $modelClass, string $foreignKey, int $localKeyValue): array
    {
        $targetModel = new $modelClass();
        return $targetModel->where($foreignKey, $localKeyValue);
    }

    /**
     * Relationship Helper: Has One (One to One)
     *
     * @param string $modelClass Model target class
     * @param string $foreignKey
     * @param int $localKeyValue
     * @return array|null
     */
    protected function hasOne(string $modelClass, string $foreignKey, int $localKeyValue): ?array
    {
        $targetModel = new $modelClass();
        return $targetModel->findBy($foreignKey, $localKeyValue);
    }

    // Transaction Management Helpers
    public function beginTransaction(): bool
    {
        return $this->db ? $this->db->beginTransaction() : false;
    }

    public function commit(): bool
    {
        return $this->db ? $this->db->commit() : false;
    }

    public function rollBack(): bool
    {
        return $this->db ? $this->db->rollBack() : false;
    }
}
