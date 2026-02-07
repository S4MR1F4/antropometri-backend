<?php

namespace App\Exceptions;

use App\Models\Subject;
use Exception;

/**
 * Exception for duplicate subject detection.
 * Per 07_api_specification.md §3.2 (422 response)
 */
class DuplicateSubjectException extends Exception
{
    public function __construct(
        public readonly Subject $existingSubject,
        string $message = 'Subjek dengan nama dan tanggal lahir yang sama sudah ada'
    ) {
        parent::__construct($message);
    }

    public function getExistingSubject(): Subject
    {
        return $this->existingSubject;
    }
}
