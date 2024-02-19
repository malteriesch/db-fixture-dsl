<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Psv;

use TestDbAcle\DbFixtureDsl\TestDataHelper\TestDataHelper;

abstract class AbstractCell
{

    protected mixed $resolved   = null;
    protected bool  $isResolved = false;

    public function __construct(protected string $key, protected string $source, protected TestDataHelper $testDataHelper)
    {
    }


    function getSource(): string
    {
        return $this->source;
    }

    abstract function resolveAsPrimaryKey($table): mixed;

    abstract function resolveAsColumn($table, $primaryKeyValue, $rowIndex): mixed;
}