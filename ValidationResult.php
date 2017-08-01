<?php

namespace SolveX\ViewModel;

class ValidationResult
{
    /**
     * @var bool
     */
    private $ok = false;

    /**
     * @var array
     */
    private $errors = [];

    /**
     * Private ValidationResult constructor.
     *
     * @param bool $ok
     * @param array $errors
     */
    private function __construct($ok, $errors = [])
    {
        $this->ok = $ok;
        $this->errors = $errors;
    }

    public function isOk()
    {
        return $this->ok;
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

    public static function WithErrors($errors)
    {
        return new self(false, $errors);
    }
}