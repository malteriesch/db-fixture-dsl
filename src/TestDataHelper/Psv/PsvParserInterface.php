<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper\Psv;

interface PsvParserInterface 
{
    public function parsePsvTree($psvContent);
    public function parsePsv($psvTableContent);
}