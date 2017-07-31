<?php

use SolveX\ViewModel\DataAnnotations as VM;

class RegistrationViewModel extends SolveX\ViewModel\ViewModel
{
    /**
     * @VM\Required
     * @var string
     */
    public $FirstName;

    /**
     * @VM\Required
     * @var string
     */
    public $LastName;

    /**
     * @VM\Min(18)
     * @var int
     */
    public $Age;

    /**
     * @VM\Required
     * @VM\MinLength(8)
     * @var string
     */
    public $Password;

    /**
     * @VM\Required
     * @VM\MinLength(8)
     * @VM\Compare("Password")
     * @var string
     */
    public $RepeatedPassword;
}