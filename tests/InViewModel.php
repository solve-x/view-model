<?php

use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;

class InViewModel extends \SolveX\ViewModel\NonThrowableViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @VM\In({"New York", "Edinburgh", "Vienna"})
     * @var string
     */
    public $Place;
}