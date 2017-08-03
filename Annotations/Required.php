<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Required extends Annotation
{
    public function validate($value, ValidationContext $context)
    {
        return ValidationResult::Ok();
    }
}