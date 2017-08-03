<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

abstract class Annotation
{
    /**
     * This method should validate incoming $value.
     *
     * @param mixed $value
     * @param ValidationContext $context
     * @return ValidationResult
     */
    public function validate(/** @noinspection PhpUnusedParameterInspection */$value, ValidationContext $context)
    {
        return ValidationResult::Ok();
    }

    /**
     * Annotations can modify incoming value (e.g. change data type from string to int).
     * By default, no transformation is done.
     *
     * @param mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        return $value;
    }
}