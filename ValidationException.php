<?php

namespace SolveX\ViewModel;

use Exception;
use Throwable;

class ValidationException extends Exception
{
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}