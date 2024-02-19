<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class DisableForeignKeyChecksCommand extends AbstractCommand
{
    function getName(): string
    {
        return "mysql:disable-foreign-key-checks";
    }

    function execute(string ...$args): void
    {
        $this->testDataHelper->getDatabaseFacade()->execute("SET FOREIGN_KEY_CHECKS=0");
    }
}