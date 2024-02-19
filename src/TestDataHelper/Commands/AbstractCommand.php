<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Commands;

use TestDbAcle\DbFixtureDsl\TestDataHelper\TestDataHelper;

abstract class AbstractCommand
{
    protected TestDataHelper $testDataHelper;

    public function setTestDataHelper(TestDataHelper $testDataHelper): self
    {
        $this->testDataHelper = $testDataHelper;
        return $this;
    }

    abstract function getName(): string;

    abstract function execute(string ...$args): void;

    public function cleanup(): void
    {

    }

    protected function throwException(string $message): void
    {
        throw new \BadFunctionCallException("{$this->getName()}: $message");
    }

}