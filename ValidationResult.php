<?php

namespace SolveX\ViewModel;

class ValidationResult
{
    /**
     * Validation successful flag.
     *
     * @var bool
     */
    private $ok = false;

    /**
     * @var string|null
     */
    private $error = null;

    /**
     * Private ValidationResult constructor.
     *
     * @param bool $ok
     * @param string $error
     */
    private function __construct($ok, $error = null)
    {
        $this->ok = $ok;
        $this->error = $error;
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
     * Returns the error (string) or null.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
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
     * @param string $error
     * @return ValidationResult
     */
    public static function NotOk($error)
    {
        return new self(false, $error);
    }
}