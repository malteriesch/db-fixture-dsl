<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions;

class TableDataInstructionSet
{
    protected bool  $clear        = false;
    protected array $instructions = [];

    public function __construct(protected string $table)
    {
    }

    public function getTable(): string
    {
        return $this->table;
    }


    public function setClear(bool $clear): static
    {
        $this->clear = $clear;
        return $this;
    }

    public function getClear(): bool
    {
        return $this->clear;
    }

    public function addInstruction(AbstractTableDataInstruction $instruction): void
    {
        $this->instructions[] = $instruction;
    }

    public function getInstructions(): array
    {
        return $this->instructions;
    }
}