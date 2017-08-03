<?php

namespace SolveX\ViewModel\Annotations;

use Carbon\Carbon;
use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class After extends Annotation
{
    public $Date;

    public function validate($value, ValidationContext $context)
    {
        $value = Carbon::parse($value);
        $threshold = Carbon::parse($this->Date);

        if ($value->greaterThan($threshold)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value is not after a specific date!');
        }
    }
}