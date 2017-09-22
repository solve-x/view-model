<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Required extends Annotation
{
    public $AllowEmptyStrings = false;

    public $AllowEmptyArrays = false;

    public function validate($value, ValidationContext $context)
    {
        if (is_array($value)) {
            return $this->validateArray($value);
        }

        if (is_string($value)) {
            return $this->validateString($value);
        }

        throw new \InvalidArgumentException('value type does not fall into expected range.');
    }

    private function validateArray($value)
    {
        if (count($value)) {
            return ValidationResult::Ok();
        }

        if (!$this->AllowEmptyArrays) {
            return ValidationResult::NotOk('The value is an empty array!');
        }

        return ValidationResult::Ok();
    }

    private function validateString($value)
    {
        $trimmedLength = strlen(trim(utf8_decode($value)));
        if ($trimmedLength) {
            return ValidationResult::Ok();
        }

        if (!$this->AllowEmptyStrings) {
            return ValidationResult::NotOk('The value is an empty string!');
        }

        return ValidationResult::Ok();
    }
}