<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetPlaceholderCommand extends AbstractCommand
{
    function getName(): string
    {
        return "set";
    }

    function execute(string ...$args): void
    {
        $name  = $args[0] ?? $this->throwException("Name required");
        $value = $args[1] ?? $this->throwException("Value required");

        $this->testDataHelper->getPlaceholders()->setPlaceholder($name, $this->testDataHelper->getParser()->replacePlaceholders($value));
    }
}