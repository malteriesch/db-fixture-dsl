<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class MockTimeCommand extends AbstractCommand
{
    function getName(): string
    {
        return "mysql:mock-time";
    }

    function execute(string ...$args): void
    {
        $dateTime = $args[0] ?? $this->throwException("Date/Datetime required");

        $this->testDataHelper->getDatabaseFacade()->getPdo()->exec("set timestamp=UNIX_TIMESTAMP('$dateTime')");
    }
}