<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Psv;

class GenerateCell extends AbstractCell
{
    protected bool $isResolved = false;
    protected mixed $resolved = null;

    function resolveAsPrimaryKey($table): mixed
    {
        $keyValue = $this->testDataHelper->getValueGenerator()->generateSequenceKey($table);
        $this->testDataHelper->getValueGenerator()->updateSequence($table, $keyValue);
        return $keyValue;
    }
    function resolveAsColumn($table, $primaryKeyValue, $rowIndex): mixed
    {
        return $this->testDataHelper->getValueGenerator()->generate(
            $this->testDataHelper->getDatabaseStructure()->getColumnConfig($table, $this->key),
            $table,
            $rowIndex,
            $primaryKeyValue
        );
    }

}