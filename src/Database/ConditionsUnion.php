<?php

namespace Bubblegum\Database;

class ConditionsUnion implements ConditionInterface
{
    /**
     * OR condition separator
     */
    public const OR = 0;
    /**
     * OR condition separator with priority
     */
    public const OR_PRIORITY = 1;
    /**
     * AND condition separator
     */
    public const AND = 2;

    /**
     * @param Condition[] $conditions
     * @param int $type
     */
    public function __construct(protected array $conditions = [], protected int $type = self::AND)
    {}

    /**
     * @return string
     */
    public function getSqlPart(): string
    {
        return match ($this->type) {
            self::OR => implode(' OR ', $this->conditions),
            self::OR_PRIORITY => '('.implode(' OR ', $this->conditions).')',
            self::AND => implode(' AND ', $this->conditions),
        };
    }

    public function __toString(): string
    {
        return $this->getSqlPart();
    }

    public function addCondition(ConditionInterface $condition): ConditionsUnion
    {
        $this->conditions[] = $condition;
        return $this;
    }

    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function setConditions(array $conditions): ConditionsUnion
    {
        $this->conditions = $conditions;
        return $this;
    }
    public function clearConditions(): ConditionsUnion
    {
        return $this->setConditions([]);
    }
}