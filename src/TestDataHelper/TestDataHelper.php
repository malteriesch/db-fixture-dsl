<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;

use TestDbAcle\DbFixtureDsl\TestDataHelper\Commands\AbstractCommand;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseFacade;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseStructure;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Db\DatabaseUpdater;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions\InstructionsFactory;
use TestDbAcle\DbFixtureDsl\TestDataHelper\Instructions\ShadowData;

class TestDataHelper
{

    protected array $registeredCommands = [];

    public function __construct(
        protected DatabaseFacade $databaseFacade,
        protected SystemDataStore $systemDataStore,
        protected Placeholders $placeholders,
        protected DslParser $parser,
        protected ValueGenerator $valueGenerator,
        protected DatabaseUpdater $databaseUpdater,
        protected DatabaseStructure $databaseStructure,
        protected InstructionsFactory $instructionsFactory,
        protected Initializer $initializer,
        protected ShadowData $shadowData,
    )
    {

        $this->parser->setTestDataHelper($this);

        $initializer->initialise($this);

    }

    public static function create(DatabaseFacade $databaseFacade, ?\Di\Container $container = null): static
    {
        $container ??= new \DI\Container();

        if(!in_array(DatabaseFacade::class, $container->getKnownEntryNames())){
            $container->set(DatabaseFacade::class, $databaseFacade);
        }

        return $container->get(static::class);
    }

    public function getParser(): DslParser
    {
        return $this->parser;
    }



    public function getShadowData(): ?ShadowData
    {
        return $this->shadowData;
    }

    public function getPlaceholders(): Placeholders
    {
        return $this->placeholders;
    }

    public function getDatabaseStructure(): DatabaseStructure
    {
        return $this->databaseStructure;
    }

    public function getValueGenerator(): ValueGenerator
    {
        return $this->valueGenerator;
    }

    public function getSystemDataStore(): SystemDataStore
    {
        return $this->systemDataStore;
    }

    public function getDatabaseFacade(): DatabaseFacade
    {
        return $this->databaseFacade;
    }

    function execute(string $dsl, $placeholders = []): array
    {
        $this->placeholders->import($placeholders);

        foreach ($this->parser->parse($dsl) as $block) {
            if ($block['type'] == 'table') {
                $this->executeTableBlock($block['table'], $block['content']);
            } else {
                $this->executeCommand($block['command'], $block['arguments']);
            }
        }

        return $this->exportPlaceholders();
    }

    function exportPlaceholders(): array
    {
        return $this->placeholders->export();
    }

    function get($key): mixed
    {
        return $this->getPlaceholders()->getPlaceholder($key);
    }

    function cleanup()
    {
        foreach ($this->registeredCommands as $command) {
            $command->cleanup();
        }
    }

    public function registerCommand(AbstractCommand $command)
    {
        $command->setTestDataHelper($this);
        $this->registeredCommands[] = $command;
    }

    private function executeCommand(?string $commandName, array $commandParts)
    {
        foreach ($this->registeredCommands as $command) {
            /**
             * @var AbstractCommand $command
             */
            if ($command->getName() == $commandName) {
                $command->execute(...array_filter($commandParts, fn($value) => !empty($value)));
                return;
            }
        }

        throw new \Exception("Could not find command $commandName");
    }

    private function executeTableBlock($table, array $parsedPsv)
    {
        $this->databaseUpdater->updateTable($this->instructionsFactory->createDbInstructions($table, $parsedPsv));
    }

    public function markTransactionStarted()
    {
        $this->shadowData->markTransactionStarted();
    }

    public function markRollback()
    {
        $this->shadowData->markRollback();
    }

}