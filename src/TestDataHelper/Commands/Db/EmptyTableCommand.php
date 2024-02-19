<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class EmptyTableCommand extends AbstractCommand
{
    function getName(): string
    {
        return "empty-table";
    }

    function execute(string ...$args): void
    {
        $table = $args[0] ?? $this->throwException("Table name required");

        $this->testDataHelper->getDatabaseFacade()->execute("DELETE FROM $table");
        $this->testDataHelper->getShadowData()->emptyTable($table);
    }
}