<?php

namespace SolveX\ViewModel;

class ValidationResult
{
    /**
     * Validation successful flag.
     *
     * @var bool
     */
    private $ok;

    /**
     * @var ValidationResultError[]|null
     */
    private $errors;

    /**
     * @param bool $ok
     * @param array $errors
     */
    private function __construct($ok, $errors = null)
    {
        $this->ok = $ok;
        $this->errors = $errors;
    }

    /**
     * Returns true in case validation was successful.
     *
     * @return bool
     */
    public function isOk()
    {
        return $this->ok;
    }

    /**
     * Returns the error (string) together with replacements
     * (inserted into the error string during translation).
     *
     * @return ValidationResultError[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Factory method. Validation successful.
     *
     * @return ValidationResult
     */
    public static function Ok()
    {
        return new self(true);
    }

    /**
     * Factory method. Validation failed.
     *
     * @param ValidationResultError[] $errors
     * @return ValidationResult
     */
    public static function NotOk($errors)
    {
        return new self(false, $errors);
    }

    /**
     * Factory method. Validation failed.
     *
     * @param string $error
     * @param array $replacements
     * @return ValidationResult
     */
    public static function NotOkSingle($error, array $replacements = [])
    {
        $result = new self(false, [
            new ValidationResultError($error, $replacements)
        ]);

        return $result;
    }

    public static function nested(ValidationException $nestedException)
    {
        $errors = $nestedException->getErrors();

        return new self(false, $errors);
    }
}
