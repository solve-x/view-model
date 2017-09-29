<?php

use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\ViewModel;

class AddressViewModel extends ViewModel
{
    /**
     * @Required
     * @DataType(DataType::String)
     * @var string
     */
    public $Street;

    /**
     * @Required
     * @DataType(DataType::Int)
     * @var int
     */
    public $HouseNumber;

    /**
     * @DataType(AddressViewModel::class)
     * @var AddressViewModel
     */
    public $ParentAddress;

    /**
     * @DataType(RegistrationViewModel::class)
     * @var RegistrationViewModel
     */
    public $RegisteredUser;
}