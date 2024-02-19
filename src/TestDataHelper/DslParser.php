<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;


use TestDbAcle\DbFixtureDsl\TestDataHelper\Utils\ArrayUtils;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\GenerateCell;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\LiteralCell;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\PlaceholderCell;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Psv\PsvParser;

class DslParser
{

    protected TestDataHelper $testDataHelper;

    public function __construct()
    {
    }

    public function setTestDataHelper(TestDataHelper $testDataHelper): DslParser
    {
        $this->testDataHelper = $testDataHelper;
        return $this;
    }


    function parse($dsl): array
    {
        $blocks = [];

        $dsl        = implode("\n", array_map(fn($line) => trim($line), explode("\n", $dsl)));//trim all lines
        $codeBlocks = array_map(fn($line) => trim($line), explode("\n\n", $dsl));
        foreach ($codeBlocks as $block) {
            if (str_starts_with($block, "[")) {
                [$tableName, $data] = $this->parseTableBlock($block);
                $blocks[] = ['type' => 'table', 'table' => $tableName, 'content' => $data];
            } else {
                foreach (explode("\n", $block) as $line) {
                    if (str_starts_with($line, "!")) {
                        [$command, $arguments] = $this->parseCommand($line);
                        $blocks[] = ['type' => 'command', 'command' => $command, 'arguments' => $arguments];
                    }
                }

            }
        }
        return $blocks;
    }

    private function parseTableBlock($block): array
    {
        $rawParsed    = (new class () extends PsvParser {  protected function filterColumnValue($value){ return $value;} })->parsePsvTree($block);
        $tableName = array_key_first($rawParsed);
        $data      = $rawParsed[$tableName];
        foreach ($data as $index => $row) {
            foreach ($row as $key => $value) {
                $data[$index][$key] = match (true) {
                    $this->testDataHelper->getPlaceholders()->isPlaceholder($value) => new PlaceholderCell($key, $value, $this->testDataHelper),
                    $value == '*' => new GenerateCell($key, $value, $this->testDataHelper),
                    default => new LiteralCell($key, $value, $this->testDataHelper)
                };
            }
        }
        return [$tableName, $data];
    }

    private function parseCommand($line): array
    {
        $commandParts = ArrayUtils::splitByWhiteSpace(substr($line, 1));
        $command      = array_shift($commandParts);
        return [$command, $commandParts];
    }

    public function replacePlaceholders($literal)
    {
        $placeholders = $this->testDataHelper->getPlaceholders()->export();
        preg_match_all('/\{(.*?)\}/', $literal, $matches);
        if(!empty($matches[1])){
            foreach ($matches[1] as $value) {
                if(array_key_exists($value, $placeholders)){
                    $literal = str_replace("{".$value."}", $placeholders[$value], $literal);
                }
            }
        }

        return $literal;
    }
}