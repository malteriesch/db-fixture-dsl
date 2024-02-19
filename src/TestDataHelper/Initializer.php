<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\EmptyTableCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug\DebugPlaceholderCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug\DebugPlaceholderWIthStartCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Debug\DebugTableCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\DisableForeignKeyChecksCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\ImportCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\MockTimeCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\ParkTableCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\SetAutoIncrementCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\SetDefaultValueCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\SetPlaceholderCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\SetStartValueCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db\SetStrictModeCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\SetSystemValueCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\System\TreatAsBooleanCommand;

class Initializer
{
    public function initialise(TestDataHelper $testDataHelper): void
    {
        $testDataHelper->registerCommand(new ImportCommand());
        $testDataHelper->registerCommand(new MockTimeCommand());
        $testDataHelper->registerCommand(new DisableForeignKeyChecksCommand());
        $testDataHelper->registerCommand(new DebugTableCommand());
        $testDataHelper->registerCommand(new DebugPlaceholderCommand());
        $testDataHelper->registerCommand(new DebugPlaceholderWIthStartCommand());
        $testDataHelper->registerCommand(new SetStartValueCommand());
        $testDataHelper->registerCommand(new SetAutoIncrementCommand());
        $testDataHelper->registerCommand(new SetStrictModeCommand());
        $testDataHelper->registerCommand(new SetPlaceholderCommand());
        $testDataHelper->registerCommand(new TreatAsBooleanCommand());
        $testDataHelper->registerCommand(new SetDefaultValueCommand());
        $testDataHelper->registerCommand(new SetSystemValueCommand());
        $testDataHelper->registerCommand(new ParkTableCommand());
        $testDataHelper->registerCommand(new EmptyTableCommand());
    }
}