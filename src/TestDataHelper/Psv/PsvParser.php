<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Psv;


class PsvParser implements PsvParserInterface
{
    const SYMBOL_PIPE                      = '|';
    const SYMBOL_COMMENT                   = '#';
    const SYMBOL_OPEN_TABLE_DEFINITION     = '[';
    const SYMBOL_CLOSE_TABLE_DEFINITION    = ']';

    /**
     *
     * Parses a psv tree such that a structure such as
     *             [expression1]
     *             id  |first_name   |last_name
     *             10  |john         |miller
     *             20  |stu          |Smith
     *
     *             [expression2]
     *             col1  |col2    |col3
     *             1     |moo     |foo
     *             30    |miaow   |boo
     *
     *             [empty]
     *
     * Into following array
     *
     *     array("expression1" => array(
     *                   array("id" => "10",
     *                       "first_name" => "john",
     *                       "last_name" => "miller"),
     *                   array("id" => "20",
     *                       "first_name" => "stu",
     *                       "last_name" => "Smith"))
     *           ),
     *          "expression2" => array(
     *                   array("col1" => "1",
     *                       "col2" => "moo",
     *                       "col3" => "foo"),
     *                   array("col1" => "30",
     *                       "col2" => "miaow",
     *                       "col3" => "boo")))
     *          'empty' => array()
     *      )
     *
     */
    public function parsePsvTree($psvContent): array
    {
        $parsedTree                   = [];
        $contentSplitByOpeningBracket = preg_split('/\n\s*(?<!\\\\)\[/', $psvContent);

        foreach ($contentSplitByOpeningBracket as $startOfTableContent) {

            if (trim($startOfTableContent) === '') {
                continue;
            }

            [$actualContentForTable, $tableName] = $this->extractExpressionAndContent($startOfTableContent);

            $parsedTree[$tableName] = $this->parsePsv($actualContentForTable);
        }
        return $parsedTree;
    }


    /**
     *
     * parses a single bit of Psv content such that
     * id  |first_name                        |last_name
     * 10  |john                              |miller
     * #lines starting with # are ignored
     * 20  |stu  #and comments can be inline  |Smith
     *
     * gets parsed into
     *
     * array(
     *    array( "id" => "10",
     *           "first_name" => "john",
     *           "last_name" => "miller" ),
     *    array( "id" => "20",
     *           "first_name" => "stu",
     *           "last_name" => "Smith" ) )
     *
     */
    public function parsePsv($psvTableContent): array
    {
        $psvRows       = $this->psvToArrayOfLines($psvTableContent);
        $psvHeaderLine = $this->extractHeaders($psvRows);
        $headers       = $this->splitByPipe($psvHeaderLine);

        foreach ($headers as $index => $header) {
            $headers[$index] = $this->filterHeader($header);
        }

        $contentTable = [];

        foreach ($psvRows as $psvRow) {
            if ($this->skipRow($psvRow)) {
                continue;
            }

            $currentRowPsvFields     = $this->splitByPipe($psvRow);
            $psvRowFilteredValueList = array();

            foreach ($headers as $columnIndex => $psvColumnHeader) {
                if ($this->isCommented($psvColumnHeader)) {
                    continue;
                }
                $psvRowFilteredValueList[$psvColumnHeader] = $this->filterColumnValue($currentRowPsvFields[$columnIndex]);
            }

            $contentTable[] = $psvRowFilteredValueList;
        }
        return $contentTable;
    }


    protected function extractHeaders(&$psvRows): string
    {
        return array_shift($psvRows);
    }

    protected function psvToArrayOfLines($psvContent): array
    {
        return explode("\n", trim($psvContent));
    }

    protected function skipRow($row): bool
    {
        $trimmedRow = ltrim($row);
        return $trimmedRow == '' || $trimmedRow[0] == static::SYMBOL_COMMENT;
    }

    protected function splitByPipe($row): array
    {
        return $this->trimArrayElements(preg_split('/(?<!\\\\)' . preg_quote(static::SYMBOL_PIPE) . '/', $row));
    }

    protected function isCommented($subject): bool
    {
        return str_starts_with($subject, static::SYMBOL_COMMENT);
    }

    protected function trimArrayElements(array $row): array
    {
        foreach ($row as $index => $column) {
            $row[$index] = trim($column);
        }
        return $row;
    }

    protected function filterHeader($value)
    {
        $filters = [
            'stripComments' => function($value) {
                if (
                    str_contains($value, static::SYMBOL_COMMENT)
                    && !str_starts_with($value, static::SYMBOL_COMMENT)) {
                    list($valuePart,) = preg_split('/(?<!\\\\)' . static::SYMBOL_COMMENT . '/', $value);
                    return trim($valuePart);
                }

                return $value;
            }
        ];

        foreach ($filters as $filter) {
            $value = $filter($value);
        }
        return $value;
    }

    protected function filterColumnValue($value)
    {
        $filters = array(
            'stripComments'            => static::getStripCommentsFilter(),
            'convertNulls'             => function($value) {
                if ($value == 'NULL') {
                    return null;
                }

                return $value;
            },
            'replaceEscapedCharacters' => static::getEscapedCharactersFilter()
        );

        foreach ($filters as $filter) {
            $value = $filter($value);
        }

        return $value;
    }

    protected function extractExpressionAndContent($startOfTableContent): array
    {
        $startOfContentSplitByClosingBracket = preg_split('/(?<!\\\\)' . preg_quote(static::SYMBOL_CLOSE_TABLE_DEFINITION) . '/', $startOfTableContent);
        return [$startOfContentSplitByClosingBracket[1], ltrim($startOfContentSplitByClosingBracket[0], static::SYMBOL_OPEN_TABLE_DEFINITION . ' ')];
    }

    public static function getStripCommentsFilter(): \Closure
    {
        return function($value) {
            if (str_contains($value, static::SYMBOL_COMMENT)) {
                [$valuePart,] = preg_split('/(?<!\\\\)' . static::SYMBOL_COMMENT . '/', $value);
                return trim($valuePart);
            }
            return $value;
        };
    }

    public static function getEscapedCharactersFilter(): \Closure
    {
        return function(&$value) {
            if (!is_null($value)) {
                $value = str_replace('\[', '[', $value);
                $value = str_replace('\]', ']', $value);
                $value = str_replace('\#', '#', $value);
                $value = str_replace('\|', '|', $value);
            }
            return $value;
        };
    }
}
