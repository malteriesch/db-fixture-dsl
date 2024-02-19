<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class ImportCommand extends AbstractCommand
{
    protected array $included = [];

    function getName(): string
    {
        return "import";
    }

    function execute(string ...$args): void
    {
        $file = $args[0] ?? $this->throwException("File name required");

        $file = $this->testDataHelper->getSystemDataStore()->getConfigValue('asset-path') . "/$file";

        if (in_array($file, $this->included)) {
            return;
        }

        $this->included[] = $file;
        $this->testDataHelper->execute(file_get_contents($file));
    }
}