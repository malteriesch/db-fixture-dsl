<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;


use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseFacade;

class InsertDataInstruction extends AbstractTableDataInstruction
{

    public function updateShadowData(ShadowData $shadowData): void
    {
        $shadowData->logInsert($this->parent->getTable(), $this->getColumns());

    }

    public function updateDatabase(DatabaseFacade $databaseFacade): void
    {
        $databaseFacade->insert($this->parent->getTable(), $this->getColumns());
    }
}