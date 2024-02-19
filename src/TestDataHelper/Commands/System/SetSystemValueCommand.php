<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetSystemValueCommand extends AbstractCommand
{

    function getName(): string
    {
        return "set-system";
    }

    function execute(string ...$args): void
    {
        $key   = $args[0] ?? $this->throwException("Key required");
        $value = $args[1] ?? $this->throwException("Value required");

        $this->testDataHelper->getSystemDataStore()->set($key, $value);
    }
}