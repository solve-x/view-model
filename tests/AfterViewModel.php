<?php

use Carbon\Carbon;
use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;

class AfterViewModel extends \SolveX\ViewModel\NonThrowableViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::Carbon)
     * @VM\After("2017-05-01")
     * @var Carbon
     */
    public $DateOfArrival;
}