<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\PsvConverter;

class DebugTableCommand extends AbstractCommand
{
    function getName(): string
    {
        return "debug-table";
    }
    function execute(string ...$args): void
    {
        //@todo try out next:
        $table = $args[0] ?? $this->throwException("Table name required");
//        $table = array_shift($args) ?? $this->throwException("Table name required");
        $columns = array_slice($args, 1);

        if($columns){
            $data = $this->testDataHelper->getDatabaseFacade()->getRows("select ".implode(",",$columns)." from $table");
        }else{
            $data = $this->testDataHelper->getDatabaseFacade()->getRows("select * from $table");
        }

        echo "\n\n[$table]\n".(new PsvConverter())->format($data)."\n\n";
    }
}