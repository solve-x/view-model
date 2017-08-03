<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Min extends Annotation
{
    public $Min;

    public function validate($value, ValidationContext $context)
    {
        if ($value >= $this->Min) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value less than min required!');
        }
    }
}