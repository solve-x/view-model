<?php

use SolveX\ViewModel\ViewModel;

class AddressViewModel extends ViewModel
{
    /** @var string */ public $Street;
    /** @var int */ public $HouseNumber;
    /** @var AddressViewModel|null */ public $ParentAddress;
    /** @var RegistrationViewModel|null */ public $RegisteredUser;
}