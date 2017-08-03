<?php

namespace SolveX\ViewModel\Annotations;

use RuntimeException;
use SolveX\ViewModel\ValidationContext;
use SolveX\ViewModel\ValidationResult;
use Carbon\Carbon;

/**
 * @Annotation
 */
class DataType extends Annotation
{
    const String = 1;
    const Int = 2;
    const Float = 3;
    const Bool = 4;
    const Carbon = 5;

    public $Type;

    public function validate($value, ValidationContext $context)
    {
        switch ($this->Type) {
            case DataType::String: return $this->validateString($value);
            case DataType::Int: return $this->validateInt($value);
            case DataType::Float: return $this->validateFloat($value);
            case DataType::Bool: return $this->validateBool($value);
            case DataType::Carbon: return $this->validateCarbon($value);
        };

        throw new RuntimeException('Unexpected DataType!');
    }

    protected function validateString(/** @noinspection PhpUnusedParameterInspection */$value)
    {
        return ValidationResult::Ok();
    }

    protected function validateInt($value)
    {
        if (ctype_digit($value)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value not an int!');
        }
    }

    private function validateFloat($value)
    {
        if (is_numeric($value)) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value not a float!');
        }
    }

    private function validateBool($value)
    {
        if ($value === '0' || $value === '1') {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value not a bool!');
        }
    }

    /**
     * @link https://gist.github.com/jeremiahlee/2885845
     * @link http://www.pelagodesign.com/blog/2009/05/20/iso-8601-date-validation-that-doesnt-suck/
     *
     * @param $value
     * @return ValidationResult
     */
    private function validateCarbon($value)
    {
        $regex = '/^([\+-]?\d{4}(?!\d{2}\b))((-?)((0[1-9]|1[0-2])(\3([12]\d|0[1-9]|3[01]))?|W([0-4]\d|5[0-2])' .
            '(-?[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6])))([T\s]((([01]\d|2[0-3])((:?)[0-5]\d)?|' .
            '24\:?00)([\.,]\d+(?!:))?)?(\17[0-5]\d([\.,]\d+)?)?([zZ]|([\+-])([01]\d|2[0-3]):?([0-5]\d)?)?)?)?$/';

        if (preg_match($regex, $value) > 0) {
            return ValidationResult::Ok();
        } else {
            return ValidationResult::NotOk('Value does not match ISO 8601 date format!');
        }
    }

    /**
     * @inheritdoc
     */
    public function transform($value)
    {
        switch ($this->Type) {
            case DataType::String: return $value;
            case DataType::Int: return (int) $value;
            case DataType::Float: return (float) $value;
            case DataType::Bool: return ($value === '1');
            case DataType::Carbon: return Carbon::parse($value);
        };

        throw new RuntimeException('Unexpected DataType!');
    }
}