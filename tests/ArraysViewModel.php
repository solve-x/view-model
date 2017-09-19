<?php

use SolveX\ViewModel\ViewModel;
use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;

class ArraysViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::IntArray)
     */
    public $IdsArray;

    /**
     * @VM\Required
     * @VM\DataType(DataType::FloatArray)
     */
    public $PricesArray;

    /**
     * @VM\Required
     * @VM\DataType(DataType::StringArray)
     */
    public $NamesArray;
}