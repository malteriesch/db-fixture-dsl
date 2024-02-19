<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;


use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseFacade;

abstract class AbstractTableDataInstruction
{

    protected array $columns = [];
    protected TableDataInstructionSet $parent;

    public function __construct(TableDataInstructionSet $parent)
    {
        $this->parent = $parent;
    }

    function addColumn($key, $value)
    {
        $this->columns[$key]  = $value;
    }

    function addColumns(array $columns)
    {
        $this->columns = array_merge($this->columns,  $columns);
    }

    public function getColumns(): array
    {
        return $this->columns;
    }

    abstract public function updateShadowData(ShadowData $shadowData);
    abstract public function updateDatabase(DatabaseFacade $databaseFacade);
}