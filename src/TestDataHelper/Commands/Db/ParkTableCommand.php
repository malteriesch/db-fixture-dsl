<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class ParkTableCommand extends AbstractCommand
{
    protected array $tables = [];

    function getName(): string
    {
        return "park-table";
    }

    function execute(string ...$ags): void
    {
        $table = $args[0] ?? $this->throwException("Table name required");

        $this->testDataHelper->getDatabaseFacade()->execute("RENAME TABLE $table to __$table");
        $this->testDataHelper->getDatabaseFacade()->execute("CREATE TABLE $table as (select * from __$table)");
        $this->testDataHelper->getDatabaseFacade()->execute("ALTER TABLE $table MODIFY COLUMN {$table}_id INT PRIMARY KEY auto_increment ");

        $this->tables[] = $table;
    }

    function cleanup(): void
    {
        foreach ($this->tables as $table) {
            $this->testDataHelper->getDatabaseFacade()->execute("DROP TABLE IF EXISTS $table");
            $this->testDataHelper->getDatabaseFacade()->execute("RENAME TABLE __$table to $table");
        }
    }
}