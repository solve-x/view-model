<?php

use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;

class NestedViewModel extends \SolveX\ViewModel\NonThrowableViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @var string
     */
    public $FirstName;

    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @var string
     */
    public $LastName;

    /**
     * @VM\Required
     * @VM\DataType(DataType::Int)
     * @VM\Min(30)
     * @var integer
     */
    public $Age;

    /**
     * @VM\Required
     * @VM\DataType(AddressViewModel::class)
     * @var AddressViewModel
     */
    public $Address;

    /**
     * @VM\Required
     * @VM\DataType(AddressViewModel::class)
     * @var AddressViewModel
     */
    public $BackupAddress;
}
