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
     * @var array
     */
    private $replacements = [];

    /**
     * Private ValidationResult constructor.
     *
     * @param bool $ok
     * @param string $error
     */
    private function __construct($ok, $error = null, $replacements = [])
    {
        $this->ok = $ok;
        $this->error = $error;
        $this->replacements = $replacements;
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
     * @return array
     */
    public function getErrorWithReplacements()
    {
        return [$this->error, $this->replacements];
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
     * @param array $replacements
     * @return ValidationResult
     */
    public static function NotOk($error, $replacements = [])
    {
        return new self(false, $error, $replacements);
    }
}
