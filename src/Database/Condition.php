<?php

namespace Bubblegum\Database;

class Condition implements ConditionInterface
{
    public function __construct(protected string $name, protected string $comparison, protected mixed $value)
    { }

    public function getName(): string
    {
        return $this->name;
    }

    public function getComparison(): string
    {
        return $this->comparison;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->getSqlPart();
    }

    public function getSqlPart(): string
    {
        return $this->name . $this->comparison . $this->valueToSql();
    }

    public function valueToSql(): string
    {
        return match (gettype($this->value)) {
            'string' => "'$this->value'",
            default => (string) $this->value,
        };
    }
}