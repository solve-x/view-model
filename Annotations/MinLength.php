<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class MinLength extends Annotation
{
    public $MinLength;

    public function validate($value, ValidationContext $context)
    {
        if (strlen($value) >= $this->MinLength) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value not long enough!');
        }
    }
}