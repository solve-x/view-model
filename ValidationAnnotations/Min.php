<?php

namespace SolveX\ViewModel\ValidationAnnotations;

use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Min extends ValidationAnnotation
{
    public $Min;

    public function validate($value, $data = null, $property = null)
    {
        if ($value >= $this->Min) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::WithErrors(['Value less than min required!']);
        }
    }
}