<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Db;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions\TableDataInstructionSet;

class DatabaseUpdater
{

    public function __construct(
        protected DatabaseFacade $databaseFacade,
    ) {
    }

    public function updateTable(TableDataInstructionSet $instructions): void
    {
        $table = $instructions->getTable();
        if($instructions->getClear()){
            $this->databaseFacade->execute("DELETE FROM $table");
        }

        foreach($instructions->getInstructions() as $instruction){
            $instruction->updateDatabase($this->databaseFacade);
        }
    }

}