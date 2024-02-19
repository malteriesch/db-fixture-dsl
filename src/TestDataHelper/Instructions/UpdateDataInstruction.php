<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;


use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseFacade;

class UpdateDataInstruction extends AbstractTableDataInstruction
{

    public function __construct(TableDataInstructionSet $parent, protected string $keyValue)
    {
        parent::__construct($parent);
    }

    public function updateShadowData(ShadowData $shadowData): void
    {
        $shadowData->logUpdate($this->parent->getTable(), $this->getColumns(), $this->keyValue);
    }

    public function updateDatabase(DatabaseFacade $databaseFacade): void
    {
        $databaseFacade->updateById($this->parent->getTable(), $this->getColumns(), $this->keyValue);
    }
}