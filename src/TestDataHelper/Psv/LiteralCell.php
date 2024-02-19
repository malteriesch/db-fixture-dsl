<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Psv;


class LiteralCell extends AbstractCell
{

    function resolveAsPrimaryKey($table): mixed
    {
        $keyValue = $this->filterColumnValue($this->getSource());
        $this->testDataHelper->getValueGenerator()->updateSequence($table, $keyValue);
        return $keyValue;
    }
    function resolveAsColumn($table, $primaryKeyValue, $rowIndex): mixed
    {
        return $this->filterColumnValue($this->getSource());
    }

    protected function filterColumnValue($value)
    {
        $filters = [
            'stripComments'            => PsvParser::getStripCommentsFilter(),
            'convertNulls'             => function ($value) {
                if ($value == 'NULL') {
                    return null;
                }

                return $value;
            },
            'replaceEscapedCharacters' => PsvParser::getEscapedCharactersFilter(),
            'addPlaceholders' =>
                $this->testDataHelper->getParser()->replacePlaceholders(...)
        ];

        foreach ($filters as $filter) {
            $value = $filter($value);
        }

        return $value;
    }
}