# ViewModel

[Model validation in ASP.NET Core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/models/validation)

Note: this library is still in beta. We might make incompatible changes until version 1.

A short example:

```php
<?php

namespace App\ViewModels;

use SolveX\ViewModel\ViewModel;

class RegistrationViewModel extends ViewModel
{
    /**
     * @var string
     */
    public $FirstName;
    
    /**
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
        // At this point $model is ready to use.
        // In case binding or validation fails, an exception is thrown during model construction.

        // $model->FirstName
        // $model->Age
    }
}
```

## Laravel integration
