<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;

class SetStrictModeCommand extends AbstractCommand
{

    protected ?string $originalMode = null;

    function getName(): string
    {
        return "mysql:set-strict-mode";
    }

    function execute(...$args): void
    {
        if ($this->originalMode === null) {
            $this->originalMode = $this->testDataHelper->getDatabaseFacade()->getSingleValue("SELECT @@sql_mode");
        }

        $this->testDataHelper->getDatabaseFacade()->execute("SET SESSION sql_mode = 'STRICT_ALL_TABLES'");
    }

    public function cleanup(): void
    {
        $this->testDataHelper->getDatabaseFacade()->execute("SET SESSION sql_mode = '{$this->originalMode}'");
    }
}