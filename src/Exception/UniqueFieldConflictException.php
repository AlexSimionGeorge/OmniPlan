<?php

namespace App\Exception;

use Exception;

class UniqueFieldConflictException extends Exception
{
    private array $conflictingFields;

    public function __construct(array $conflictingFields)
    {
        $this->conflictingFields = $conflictingFields;
        parent::__construct("Conflicting unique fields");
    }

    public function getConflictingFields(): array
    {
        return $this->conflictingFields;
    }
}