<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetDefaultValueCommand extends AbstractCommand
{

    function getName(): string
    {
        return "set-default-value";
    }

    function execute(string ...$args): void
    {
        $tableColumn = $args[0] ?? $this->throwException("Table/Column required");
        $value       = $args[1] ?? $this->throwException("Value required");

        if(!str_contains($tableColumn, ".")) {
            $this->throwException("Table/Column needs to be in format 'table.column'");
        }

        [$table, $column] = explode(".", $tableColumn);


        if ($this->testDataHelper->getPlaceholders()->isPlaceholder($value)) {
            $value = $this->testDataHelper->getPlaceholders()->getPlaceholder($value);
        }

        $defaultValues          = $this->testDataHelper->getSystemDataStore()->getDefaultValues($table);
        $defaultValues[$column] = $value;

        $this->testDataHelper->getSystemDataStore()->set("default-values.$table", $defaultValues);
    }
}