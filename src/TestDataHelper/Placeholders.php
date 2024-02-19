<?php

namespace TestDbAcle\DbFixtureDsl\TestDataHelper;

class Placeholders
{
    private array $placeholders = [];

    public function set(string $key, $value): self
    {
        $this->placeholders[$key] = $value;
        return $this;
    }


    public function isPlaceholder($value)
    {
        return str_starts_with($value, '%');
    }

    function extractPlaceholderName($value)
    {
        return ltrim($value, '%');
    }

    public function getPlaceholder(string $key, $default = null): mixed
    {
        $key = $this->extractPlaceholderName($key);
        return $this->placeholders[$key] ?? $default;
    }
    public function getPlaceholderOrResolve(string $key, callable $fn): mixed
    {
        $key = $this->extractPlaceholderName($key);
        if (!array_key_exists($key, $this->placeholders)) {
            $this->placeholders[$key] = $fn();
        }
        return $this->placeholders[$key];
    }
    public function hasPlaceholder(string $key): bool
    {
        $key = $this->extractPlaceholderName($key);
        return array_key_exists($key, $this->placeholders);
    }


    public function setPlaceholder(string $key, mixed $value): self
    {
        $key = $this->extractPlaceholderName($key);
        if (isset($this->placeholders[$key])) {
            return $this;
        }
        $this->placeholders[$key] = $value;
        return $this;
    }

    public function import(mixed $placeholders)
    {
        $this->placeholders = [...$this->placeholders, ...$placeholders];
    }
    public function export(): array
    {
        return $this->placeholders;
    }
}