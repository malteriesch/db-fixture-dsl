<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Filters;

//@todo is used? create example
class ColumnFilters
{
    private array $columnFilters      = [];
    public function registerColumnFilter(AbstractColumnFilter $filter): void
    {
        $this->columnFilters[] = $filter;
    }

    public function filter(string $table, string $columnName, mixed $value, int $rowIndex): mixed
    {
        foreach ($this->columnFilters as $columnFilter) {
            if(!$columnFilter->match($table, $columnName)){
                continue;
            }
            $value = $columnFilter->filter($table, $columnName, $value, $rowIndex);
        }
        return $value;
    }
}