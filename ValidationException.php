<?php

namespace SolveX\ViewModel;

use Exception;

class ValidationException extends Exception
{
    /**
     * @var array
     */
    private $errors;

    /**
     * Constructor.
     *
     * @param string $message The internal exception message
     * @param array $errors
     * @param int $code The internal exception code
     * @param Exception $previous The previous exception
     */
    public function __construct($message = null, $errors = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
