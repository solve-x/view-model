<?php

namespace SolveX\ViewModel;

class ValidationResultError
{
    /**
     * @var string
     */
    private $message;

    /**
     * @var array
     */
    private $replacements;

    /**
     * @param string $message
     * @param array $replacements
     */
    public function __construct($message, array $replacements = null)
    {
        $this->message = $message;
        $this->replacements = $replacements;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return array
     */
    public function getReplacements()
    {
        return $this->replacements;
    }
}
