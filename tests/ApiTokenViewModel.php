<?php

use Carbon\Carbon;
use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\ViewModel;

class ApiTokenViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @VM\MinLength(16)
     * @var string
     */
    public $Value;

    /**
     * @VM\Required
     * @VM\DataType(DataType::Carbon)
     * @var Carbon
     */
    public $ValidFrom;
}