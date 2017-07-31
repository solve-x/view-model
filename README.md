# ViewModel

[Model validation in ASP.NET Core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/models/validation)

Current proposal:

```php
<?php

namespace App\ViewModels;

use SolveX\ViewModel\ViewModel;
use SolveX\ViewModel\DataAnnotations as VM;

class RegistrationViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @var string
     */
    public $FirstName;

    /**
     * @VM\Min(18)
     * @var int
     */
    public $Age;
}
```

```php
<?php

namespace App\Controllers;

use App\ViewModels\RegistrationViewModel;

class UserController
{
    public function register(RegistrationViewModel $model)
    {
        if ($model->IsValid) {
            // $model->FirstName
            // $model->Age
        }
    }
}
```