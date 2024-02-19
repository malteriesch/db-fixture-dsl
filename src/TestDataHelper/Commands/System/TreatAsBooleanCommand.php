<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class TreatAsBooleanCommand extends AbstractCommand
{
    function getName(): string
    {
        return "treat-as-boolean";
    }

    function execute(string ...$args): void
    {
        $table  = $args[0] ?? $this->throwException("Table name required");
        $column = $args[1] ?? $this->throwException("Column name required");

        $this->testDataHelper->getSystemDataStore()->set("is.boolean.$table.$column", true);
    }
}