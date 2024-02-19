<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetStartValueCommand extends AbstractCommand
{
    function getName(): string
    {
        return "set-start-value";
    }

    function execute(string ...$args): void
    {
        $table = $args[0] ?? $this->throwException("Table name required");
        $start = $args[1] ?? $this->throwException("Start value required");

        $this->testDataHelper->getValueGenerator()->updateSequence($table, $start - 1);
    }
}