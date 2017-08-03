<?php

use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\ViewModel;

class InViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @VM\In({"New York", "Edinburgh", "Vienna"})
     * @var string
     */
    public $Place;
}