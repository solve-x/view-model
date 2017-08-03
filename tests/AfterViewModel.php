<?php

use Carbon\Carbon;
use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\ViewModel;

class AfterViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::Carbon)
     * @VM\After("2017-05-01")
     * @var Carbon
     */
    public $DateOfArrival;
}