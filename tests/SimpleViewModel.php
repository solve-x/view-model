<?php

use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\NonThrowableViewModel;

class SimpleViewModel extends NonThrowableViewModel
{
    /**
     * @VM\DataType(DataType::String)
     * @VM\DefaultValue("Joe")
     * @var string
     */
    public $FirstName;
}