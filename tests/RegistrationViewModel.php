<?php

use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\ViewModel;

class RegistrationViewModel extends ViewModel
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
     * @VM\DataType(DataType::Int)
     * @VM\Min(18)
     * @var int
     */
    public $Age;

    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @VM\MinLength(8)
     * @var string
     */
    public $Password;

    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @VM\MinLength(8)
     * @VM\Compare("Password")
     * @var string
     */
    public $RepeatedPassword;

    /**
     * @VM\DataType(DataType::Bool)
     * @var boolean
     */
    public $RememberMe;
}