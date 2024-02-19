<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;

class SystemDataStore
{

    private array $dataStore = [];

    public function set(string $key, $value):self
    {
        $this->dataStore[$key] = $value;
        return $this;
    }

    public function get(string $key, $default = null): mixed
    {
        return $this->dataStore[$key] ?? $default;
    }


    public function getDefaultValues($table): mixed
    {
        return $this->get("default-values.$table", []);
    }

    public function getConfigValue($name): mixed
    {
        return $this->get("config.$name");
    }

    public function import(array $config)
    {
        foreach($config as $key=>$value){
            $this->set($key, $value);
        }
    }

    public function export(): array
    {
        return $this->dataStore;
    }
}