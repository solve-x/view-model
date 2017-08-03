<?php

namespace SolveX\ViewModel;

use Exception;

class ValidationException extends Exception
{
    /**
     * Constructor.
     *
     * @param string     $message  The internal exception message
     * @param int        $code     The internal exception code
     * @param Exception  $previous The previous exception
     */
    public function __construct($message = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}