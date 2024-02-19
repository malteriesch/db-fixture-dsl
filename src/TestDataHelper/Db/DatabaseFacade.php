<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Db;


use PDO;

class DatabaseFacade
{

    public PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function getSingleValue($sql, array $bindings = []): mixed
    {
        $results = $this->getRows($sql, $bindings);
        if (!$results) {
            return null;
        }
        return array_pop($results[0]);
    }

    public function getRows($sql, array $bindings = []): array
    {
        try {
            $statement = $this->pdo->prepare($sql);
            foreach ($bindings as $key => $value) {
                $statement->bindValue($key, $value);
            }
            $statement->execute();
            $toReturn  = $statement->fetchAll(PDO::FETCH_ASSOC);
            $statement = null;
            return $toReturn;
        } catch (\PDOException $e) {
            $this->throwException($e, $sql, $bindings);
        }
    }


    public function getRowsAndHeaders($sql, array $bindings = []): array
    {

        $statement = $this->pdo->prepare($sql);
        $statement->execute($bindings);

        $headers = [];
        for ($index = 0; $column = $statement->getColumnMeta($index); $index++) {
            $headers[] = $column['name'];
        }

        return ['rows' => $statement->fetchAll(PDO::FETCH_ASSOC), 'headers' => $headers];
    }

    public function insert($table, array $values): int
    {
        $columnNamesImploded = implode(', ', array_keys($values));

        $valuesToInsert = [];
        $bindings       = [];

        foreach ($values as $columnName => $value) {
            $valuesToInsert[]      = ':' . $columnName;
            $bindings[$columnName] = $value;
        }
        $valuesToInsertImploded = implode(', ', $valuesToInsert);
        $sql                    = "INSERT INTO `$table` ( {$columnNamesImploded} ) VALUES ( {$valuesToInsertImploded} )";

        try {
            $this->execute($sql, $bindings);
            return $this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            $this->throwException($e, $sql, $bindings);
        }
    }


    function getInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    public function update($table, array $values, $conditionString, array $conditionBindings = []): void
    {
        $updates  = [];
        $bindings = [];

        foreach ($values as $columnName => $value) {
            $updates[]             = "{$columnName} = " . ':' . $columnName;
            $bindings[$columnName] = $value;
        }

        $bindings        = array_merge($bindings, $conditionBindings);
        $updatesImploded = implode(', ', $updates);
        $sql             = "UPDATE `{$table}` SET $updatesImploded WHERE $conditionString";

        try {
            $this->execute($sql, $bindings);
        } catch (\PDOException $e) {
            $this->throwException($e, $sql, $bindings);
        }
    }

    public function execute($sql, $bindings = []): void
    {
        $this->pdo->prepare($sql)->execute($bindings);
    }

    public function getSingleRow($sql, array $bindings = []): ?array
    {
        $rows = $this->getRows($sql, $bindings);

        if (!$rows) {
            return null;
        }

        if (count($rows) > 1) {
            $this->throwException(new \Exception("SQL Error: was expecting one row, found more"), $sql, $bindings);
        }

        return $rows[0];
    }

    public function updateById($table, $values, $idValue, $idColumn = null):void
    {
        if (!$idColumn) {
            $idColumn = "{$table}_id";
        }

        $this->update($table, $values, "$idColumn=:PRIMARY_KEY", ['PRIMARY_KEY' => $idValue]);
    }

    protected function throwException(\Exception $e, $sql, $bindings)
    {
        throw new \PDOException("SQL ERROR IN: \n" . $e->getMessage() . "\n---------------------------------------\n$sql", 0, $e);
    }

    public function beginTransaction(): void
    {
        $this->pdo->beginTransaction();
    }

    public function rollBack(): void
    {
        $this->pdo->rollBack();
    }

    public function commit(): void
    {
        $this->pdo->commit();
    }
}
