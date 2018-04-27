<?php

namespace SolveX\ViewModel;

class ViewModelException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}