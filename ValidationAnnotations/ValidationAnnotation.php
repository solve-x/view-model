<?php

namespace SolveX\ViewModel\ValidationAnnotations;

use SolveX\ViewModel\DataSourceInterface;
use SolveX\ViewModel\ValidationResult;
use ReflectionProperty;

abstract class ValidationAnnotation
{
    /**
     * This method must be implemented in specific validation annotation classes!
     *
     * @param mixed $value
     * @param DataSourceInterface|null $data
     * @param ReflectionProperty|null $property
     * @return ValidationResult
     */
    abstract public function validate($value, $data = null, $property = null);
}