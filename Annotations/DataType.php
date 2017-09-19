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
    const IntArray = 6;
    const FloatArray = 7;
    const StringArray = 8;

    public $Type;

    public function validate($value, ValidationContext $context)
    {
        switch ($this->Type) {
            case static::String: return $this->validateString($value);
            case static::Int: return $this->validateInt($value);
            case static::Float: return $this->validateFloat($value);
            case static::Bool: return $this->validateBool($value);
            case static::Carbon: return $this->validateCarbon($value);
            case static::IntArray: return $this->validateIntArray($value);
            case static::FloatArray: return $this->validateFloatArray($value);
            case static::StringArray: return $this->validateStringArray($value);
        }

        throw new RuntimeException('Unexpected DataType!');
    }

    protected function validateString(/** @noinspection PhpUnusedParameterInspection */$value)
    {
        return ValidationResult::Ok();
    }

    protected function validateInt($value)
    {
        if (!is_string($value)) {
            return ValidationResult::NotOk('Input not a string!');
        }

        if (false === filter_var($value, FILTER_VALIDATE_INT)) {
            return ValidationResult::NotOk('Value not an int!');
        }

        return ValidationResult::Ok();
    }

    private function validateFloat($value)
    {
        if (!is_string($value)) {
            return ValidationResult::NotOk('Input not a string!');
        }

        if (false === filter_var($value, FILTER_VALIDATE_FLOAT)) {
            return ValidationResult::NotOk('Value not a float!');
        }

        return ValidationResult::Ok();
    }

    private function validateBool($value)
    {
        $booleanStrings = [
            '0', '1', 'on', 'off', 'yes', 'no', 'true', 'false'
        ];

        if (in_array($value, $booleanStrings, true)) {
            return ValidationResult::Ok();
        }

        return ValidationResult::NotOk('Value not a bool!');
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
        }

        return ValidationResult::NotOk('Value does not match ISO 8601 date format!');
    }

    private function validateIntArray($value)
    {
        return $this->validateArray($value, 'int', 'filter_var', FILTER_VALIDATE_INT);
    }

    private function validateFloatArray($value)
    {
        return $this->validateArray($value, 'float', 'filter_var', FILTER_VALIDATE_FLOAT);
    }

    private function validateStringArray($value)
    {
        if (!is_array($value)) {
            return ValidationResult::NotOk('Value not an array!');
        }

        return ValidationResult::Ok();
    }

    private function validateArray($value, $type, $callback, $callbackParams = null)
    {
        if (!is_array($value)) {
            return ValidationResult::NotOk('Value not an array!');
        }

        foreach ($value as $element) {
            if (!$callback($element, $callbackParams)) {
                return ValidationResult::NotOk("Value :value not {$type}!", [
                    'value' => $element
                ]);
            }
        }

        return ValidationResult::Ok();
    }

    /**
     * @inheritdoc
     * @throws \RuntimeException
     */
    public function transform($value)
    {
        switch ($this->Type) {
            case static::String: return $value;
            case static::Int: return (int) $value;
            case static::Float: return (float) $value;
            case static::Bool: return (
                '1' === $value ||
                'on' === $value ||
                'true' === $value ||
                'yes' === $value
            );
            case static::Carbon: return Carbon::parse($value);
            case static::IntArray: return $this->arrayTransform($value, 'intval');
            case static::FloatArray: return $this->arrayTransform($value, 'floatval');
            case static::StringArray: return $value;
        }

        throw new RuntimeException('Unexpected DataType!');
    }

    private function arrayTransform($array, $callback)
    {
        foreach ($array as $key => &$value) {
            $value = $callback($value);
        }

        return $array;
    }
}