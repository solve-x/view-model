<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Compare extends Annotation
{
    public $OtherProperty;

    public function validate($value, ValidationContext $context)
    {
        $data = $context->getData();

        if (! $data->has($this->OtherProperty)) {
            return ValidationResult::NotOk('Other property missing!');
        }

        if ($value == $data->get($this->OtherProperty)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value does not match the value of another property!');
        }
    }
}