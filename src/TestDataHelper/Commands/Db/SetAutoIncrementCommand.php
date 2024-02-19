<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetAutoIncrementCommand extends AbstractCommand
{
    function getName(): string
    {
        return "mysql:set-autoincrement";
    }

    function execute(string ...$args): void
    {
        $table = $args[0] ?? $this->throwException("Table name required");
        $start = $args[1] ?? $this->throwException("Start value required");

        $this->testDataHelper->getDatabaseFacade()->execute("alter table $table AUTO_INCREMENT=$start");
    }
}