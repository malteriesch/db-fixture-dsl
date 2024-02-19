<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class DebugPlaceholderCommand extends AbstractCommand
{
    function getName(): string
    {
        return "debug";
    }

    function execute(string ...$args): void
    {
        $name = $args[0] ?? null;

        if ($name) {
            echo "\n\n%" . $this->testDataHelper->getPlaceholders()->extractPlaceholderName($name) . ": " . $this->testDataHelper->getPlaceholders()->getPlaceholder($name) . "\n\n";
        } else {
            $values = $this->testDataHelper->getPlaceholders()->export();
            ksort($values);
            foreach ($values as $key => $value) {
                $values[$key] = "%$key: $value";
            }
            echo "\n\n" . implode("\n", $values) . "\n\n";
        }
    }
}