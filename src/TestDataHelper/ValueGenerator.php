<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;

class ValueGenerator
{
    protected SystemDataStore $dataStore;

    protected array $sequences = [];

    public function __construct(SystemDataStore $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    function generate(array $config, string $table, int $rowIndex, int $primaryKeyValue)
    {
        $column = $config['Field'];
        $type = strtolower($config['Type']);
        return match(true){//all assumed unsigned
            str_ends_with($column, '_id')=>$this->generateSequenceKey(substr($column,0,-3)),
            str_contains($type, 'tinyint') =>
                ($this->dataStore->get("is.boolean.$table.$column")) ?
                    ($primaryKeyValue % 2)+1
                    :
                    ($primaryKeyValue % 255)+1
                    ,
            str_contains($type, 'smallint') => ($primaryKeyValue % 65535)+1,
            str_contains($type, 'int') => ($primaryKeyValue % 1000000)+1,
            str_contains($type, 'varchar'),
            str_contains($type, 'text') => $column."-".$primaryKeyValue,
            default => 1
        };
    }

    function generateSequenceKey(string $table)
    {
        if (!isset($this->sequences[$table])) {
            $this->sequences[$table] = (crc32($table) % 1000) * 1000;
        }

        return $this->sequences[$table]++;
    }


    public function updateSequence(string $table, int $newKeyValue)
    {
        if ($newKeyValue < ($this->sequences[$table] ?? 0)) {
            return;
        }
        $this->sequences[$table] = $newKeyValue + 1;
    }
}