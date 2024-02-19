<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Db;


class DatabaseStructure
{
    private DatabaseFacade    $databaseFacade;
    private array $tableDescriptions  = [];

    public function __construct(DatabaseFacade $databaseFacade)
    {
        $this->databaseFacade = $databaseFacade;
    }

    public function getOrCreateTableDescription(string $table): array
    {
        if(!isset($this->tableDescriptions[$table])){
            $config                          = $this->databaseFacade->getRows("describe $table");
            $this->tableDescriptions[$table] = $config;
        }
        return $this->tableDescriptions[$table] ;
    }

    public function getNullColumns($table): array
    {
        return (array_filter($this->getOrCreateTableDescription($table),
            fn($config) => $config['Null'] == 'NO' && $config['Key'] != 'PRI' && empty($config['Default'])));
    }

    public function isPrimaryKey(string $table, $columnName): bool
    {
        $config = $this->getOrCreateTableDescription($table);
        return (array_column(array_filter($config, fn($value) => $value['Field'] == $columnName), 'Key')[0] ?? '') == 'PRI';
    }
    public function getPrimaryKeyColumn(string $table,): string
    {
        $config = $this->getOrCreateTableDescription($table);
        return (array_filter($config, fn($value) => $value['Key'] == 'PRI'))[0]['Field'];
    }
    public function getNonPrimaryKeyColumns(string $table,): array
    {
        $config = $this->getOrCreateTableDescription($table);
        return array_column(array_filter($config, fn($value) => $value['Key'] != 'PRI'), 'Field');
    }
    public function getColumnConfig(string $table, $column): array
    {
        $config = $this->getOrCreateTableDescription($table);
        return array_values(array_filter($config, fn($value) => $value['Field'] === $column))[0];
    }
}