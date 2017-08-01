<?php

namespace SolveX\ViewModel\ValidationAnnotations;

use SolveX\ViewModel\DataSourceInterface;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Compare extends ValidationAnnotation
{
    public $OtherProperty;

    public function validate($value, $data = null, $property = null)
    {
        /** @var DataSourceInterface $data */
        if ($value == $data->get($this->OtherProperty)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::WithErrors(['Value does not match the value of another property!']);
        }
    }
}