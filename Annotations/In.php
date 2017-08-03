<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class In extends Annotation
{
    public $Values;

    public function validate($value, ValidationContext $context)
    {
        if (in_array($value, $this->Values)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Must be one of specified values!');
        }
    }
}