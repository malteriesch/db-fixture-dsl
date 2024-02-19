<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class DebugPlaceholderWIthStartCommand extends AbstractCommand
{
    function getName(): string
    {
        return "debug-starts-with";
    }
    function execute(string ...$args): void
    {
        $name = $args[0] ?? $this->throwException("Name required");

        $toDisplay = [];
        $values = $this->testDataHelper->getPlaceholders()->export();
        ksort($values);
        foreach($values as $key => $value){
            if(\str_starts_with($key, $name)){
                $toDisplay[$key] = "%$key: $value";
            }
        }
        echo "\n\n" . implode("\n",$toDisplay)."\n\n";
    }
}