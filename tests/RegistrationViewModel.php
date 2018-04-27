<?php

use SolveX\ViewModel\ViewModel;

class RegistrationViewModel extends ViewModel
{
    /** @var string */ public $FirstName;
    /** @var string */ public $LastName;
    /** @var int|null */ public $Age;
    /** @var string */ public $Password;
    /** @var string */ public $RepeatedPassword;
    /** @var boolean|null */ public $RememberMe;
}