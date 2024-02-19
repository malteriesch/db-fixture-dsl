<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseStructure;
use TestDbAcle\DbFixtureDsl\TestDataHelper\SystemDataStore;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Utils\ArrayUtils;
use TestDbAcle\DbFixtureDsl\TestDataHelper\ValueGenerator;

class InstructionsFactory
{

    public function __construct(
        protected ShadowData        $shadowData,
        protected DatabaseStructure $databaseStructure,
        protected ValueGenerator    $valueGenerator,
        protected SystemDataStore   $dataStore
    )
    {
    }

    public function getShadowData(): ShadowData
    {
        return $this->shadowData;
    }

    public function createDbInstructions($table, $pass1): TableDataInstructionSet
    {
        $instructions = (new TableDataInstructionSet($table))->setClear($this->shadowData->tableIsEmpty($table));

        $primaryKeyColumn = $this->databaseStructure->getPrimaryKeyColumn($table);

        $nonPrimaryKeyColumns = $this->databaseStructure->getNonPrimaryKeyColumns($table);
        foreach ($pass1 as $rowIndex => $row) {
            if (empty($row)) {
                throw new \Exception("row cannot be empty");
            }

            $keyValue = isset($row[$primaryKeyColumn]) ? $row[$primaryKeyColumn]->resolveAsPrimaryKey($table) : $this->valueGenerator->generateSequenceKey($table);

            if ($this->shadowData->entryExists($table, $keyValue)) {
                $instruction = new UpdateDataInstruction($instructions, $keyValue);
            } else {
                $instruction = new InsertDataInstruction($instructions);
                $instruction->addColumn($primaryKeyColumn, $keyValue);
                $instruction->addColumns($this->addDefaultValues($table));
                $instruction->addColumns($this->addNonNullColumns($table, $rowIndex, $keyValue));
            }

            $allNonPrimaryKeyColumns = ArrayUtils::allWithKeys($row, $nonPrimaryKeyColumns);
            foreach ($allNonPrimaryKeyColumns as $key => $cell) {
                $instruction->addColumn($key, $cell->resolveAsColumn($table, $keyValue, $rowIndex));
            }

            $instructions->addInstruction($instruction);
            $instruction->updateShadowData($this->shadowData);
        }
        return $instructions;
    }

    private function addNonNullColumns(string $table, int $rowIndex, int $primaryKeyValue): array
    {
        $upsert   = [];
        $nonNulls = $this->databaseStructure->getNullColumns($table);
        foreach ($nonNulls as $config) {
            if (isset($upsert[$config['Field']])) {
                continue;
            }
            $upsert[$config['Field']] = $this->valueGenerator->generate($config, $table, $rowIndex, $primaryKeyValue);
        }
        return $upsert;
    }

    private function addDefaultValues($table): array
    {
        $upsert = [];
        foreach ($this->dataStore->getDefaultValues($table) as $column => $value) {
            if (!isset($upsert[$column])) {
                $upsert[$column] = $value;
            }
        }
        return $upsert;
    }
}