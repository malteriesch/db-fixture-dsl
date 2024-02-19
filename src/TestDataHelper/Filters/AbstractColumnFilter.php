<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Filters;

use TestDbAcle\DbFixtureDsl\TestDataHelper\TestDataHelper;

class AbstractColumnFilter
{
    protected string $table = '*';
    protected string $column = '*';

    protected TestDataHelper $testDataHelper;

    public function setTestDataHelper(TestDataHelper $testDataHelper): self
    {
        $this->testDataHelper = $testDataHelper;
        return $this;
    }

    public function match($currentTable, $currentColumn, $currentRowIndex)
    {
        return
            ($currentTable == '*' || $currentTable == $this->table) &&
            ($currentColumn == '*' || $currentColumn == $this->column);
    }

}   