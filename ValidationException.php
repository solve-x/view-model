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
     * @var null|ViewModel
     */
    private $model;

    /**
     * Constructor.
     *
     * @param string $message The internal exception message
     * @param array $errors
     * @param ViewModel $model
     * @param int $code The internal exception code
     * @param Exception $previous The previous exception
     */
    public function __construct($message = null, $errors = [], $model = null, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return null|ViewModel
     */
    public function getModel()
    {
        return $this->model;
    }
}
