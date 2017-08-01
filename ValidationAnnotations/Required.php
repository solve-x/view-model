<?php

namespace SolveX\ViewModel\ValidationAnnotations;

use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Required extends ValidationAnnotation
{
    public function validate($value, $data = null, $property = null)
    {
        return ValidationResult::Ok();
    }
}