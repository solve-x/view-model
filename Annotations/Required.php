<?php

namespace SolveX\ViewModel\Annotations;

use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;

/**
 * @Annotation
 */
class Required extends Annotation
{
    /**
     * @var bool
     */
    public $AllowEmptyStrings = false;

    /**
     * @var bool
     */
    public $AllowEmptyArrays = false;

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    public function validate($value, ValidationContext $context)
    {
        if (null === $value) {
            return ValidationResult::NotOkSingle('The $value is null!');
        }

        if (is_array($value)) {
            return $this->validateArray($value);
        }

        if (is_string($value)) {
            return $this->validateString($value);
        }

        throw new \InvalidArgumentException('Type of $value does not fall into expected range.');
    }

    private function validateArray($value)
    {
        if (count($value)) {
            return ValidationResult::Ok();
        }

        if (!$this->AllowEmptyArrays) {
            return ValidationResult::NotOkSingle('The value is an empty array!');
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
            return ValidationResult::NotOkSingle('The value is an empty string!');
        }

        return ValidationResult::Ok();
    }
}