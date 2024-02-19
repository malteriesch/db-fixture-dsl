<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Traits;

use Pdo;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseFacade;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\PsvParser;
use TestDbAcle\DbFixtureDsl\TestDataHelper\TestDataHelper;

trait TestDataHelperTrait
{
    private ?DatabaseFacade $databaseFacade = null;
    private ?TestDataHelper $testDataHelper = null;

    protected function getDatabaseFacade(): DatabaseFacade
    {
        if ($this->databaseFacade == null) {
            $this->databaseFacade = new DatabaseFacade($this->getPdo());
        }

        return $this->databaseFacade;
    }

    protected function initTestDataHelper(): void
    {
        //any common setup code can go here
        //example:

        /*
         $this->testDataHelper->execute('
                !set-system config.asset-path tests/assets
                !disable-foreign-key-checks
                !set-strict-mode
         ');
         */
    }

    protected function getPdo(): Pdo
    {
        throw new \RuntimeException("getPdo() needs to be implemented");
    }

    protected function getTestDataHelper(): TestDataHelper
    {
        if ($this->testDataHelper == null) {
            $this->testDataHelper = TestDataHelper::create($this->getDatabaseFacade());
            $this->initTestDataHelper();
        }

        return $this->testDataHelper;
    }

    /**
     * use wildcard * to ignore values
     */
    protected function assertTableStateContains($expectedPsv, $placeHolders = array(), $message = ''): void
    {
        $expectedData = (new PsvParser())->parsePsvTree($expectedPsv);

        $actualData = [];
        foreach ($expectedData as $table => $data) {
            if ($data) {
                $actualData[$table] = $this->getDatabaseFacade()->getRows("select " . implode(",", array_keys($data[0])) . " from $table");
            } else {
                $actualData[$table] = [];
            }
        }

        foreach ($expectedData as $tableName => $tableData) {
            foreach ($tableData as $rowIndex => $row) {
                foreach ($row as $key => $value) {
                    if ($value == '*') {
                        $actualData[$tableName][$rowIndex][$key] = '*';
                    }

                    if (!str_starts_with($value, "%")) {
                        continue;
                    }

                    $expectedData[$tableName][$rowIndex][$key] = $placeHolders[$value] ?? throw new \RuntimeException("Placeholder $value not found");

                    if (is_array($placeHolders[$value])) {
                        $actualData[$tableName][$rowIndex][$key] = json_decode($actualData[$tableName][$rowIndex][$key] ?? null, true);
                    }
                }
            }
        }

        $this->assertEquals($expectedData, $actualData, $message);
    }

    protected function prefixPlaceholders($placeholders): array
    {
        $out = [];
        foreach ($placeholders as $key => $value) {
            $out["%$key"] = $value;
        }

        return $out;
    }
}