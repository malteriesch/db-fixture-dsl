<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseStructure;

class ShadowData
{

    private DatabaseStructure $databaseStructure;

    private array   $data         = [];
    private ?array  $rollbackData = null;
    protected array $sequences    = [];

    public function __construct(
        DatabaseStructure $databaseStructure,
    )
    {
        $this->databaseStructure = $databaseStructure;
    }

    public function logInsert(string $table, array $upsert): void
    {
        $this->data[$table][] = $upsert;
    }

    public function logUpdate(string $tableName, array $upsert, mixed $keyValue): void
    {
        $keyColumn = $this->databaseStructure->getPrimaryKeyColumn($tableName);
        foreach ($this->data[$tableName] ?? [] as $rowIndex => $row) {
            if ($row[$keyColumn] == $keyValue) {
                foreach ($upsert as $key => $value) {
                    $this->data[$tableName][$rowIndex][$key] = $value;
                }
            }
        }
    }

    public function entryExists(string $tableName, $keyValue): bool
    {
        $keyColumn = $this->databaseStructure->getPrimaryKeyColumn($tableName);
        foreach ($this->data[$tableName] ?? [] as $rowIndex => $row) {
            if ($row[$keyColumn] == $keyValue) {
                return true;
            }
        }

        return false;
    }

    public function getEntry(string $tableName, $keyValue): ?array
    {
        $keyColumn = $this->databaseStructure->getPrimaryKeyColumn($tableName);
        foreach ($this->data[$tableName] ?? [] as $rowIndex => $row) {
            if ($row[$keyColumn] == $keyValue) {
                return $row;
            }
        }

        return null;
    }

    public function isEmpty(string $tableName): bool
    {
        return empty($this->data[$tableName]);
    }

    function getData(): array
    {
        return $this->data;
    }

    public function tableIsEmpty($table): bool
    {
        return empty($this->data[$table]);
    }

    public function markTransactionStarted(): void
    {
        $this->rollbackData = $this->data;
    }

    public function markRollback(): void
    {
        $this->data         = $this->rollbackData;
        $this->rollbackData = null;
    }

    public function emptyTable($table): void
    {
        $this->data[$table] = [];
    }

}