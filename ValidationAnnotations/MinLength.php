<?php

namespace SolveX\ViewModel\ValidationAnnotations;

use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class MinLength extends ValidationAnnotation
{
    public $MinLength;

    public function validate($value, $data = null, $property = null)
    {
        if (strlen($value) >= $this->MinLength) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::WithErrors(['Value not long enough!']);
        }
    }
}