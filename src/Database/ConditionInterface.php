<?php

namespace Bubblegum\Database;

interface ConditionInterface {
    public function getSqlPart(): string;
}