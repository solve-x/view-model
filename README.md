# ViewModel

[Model validation in ASP.NET Core MVC](https://docs.microsoft.com/en-us/aspnet/core/mvc/models/validation)

Note: this library is still in beta. We might make incompatible changes until version 1.

Short example:

```php
<?php

namespace App\ViewModels;

use SolveX\ViewModel\ViewModel;
use SolveX\ViewModel\Annotations as VM;
use SolveX\ViewModel\Annotations\DataType;

class RegistrationViewModel extends ViewModel
{
    /**
     * @VM\Required
     * @VM\DataType(DataType::String)
     * @var string
     */
    public $FirstName;

    /**
     * @VM\Min(18)
     * @VM\DataType(DataType::Int)
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
        // At this point $model is valid.
        // In case validation fails, an exception is thrown during model construction.

        // $model->FirstName
        // $model->Age
    }
}
```

## Laravel integration

## Validation annotations

- DataType(DataType::String), DataType(DataType::Int)
- Required
- DefaultValue
- Min
- MinLength
- In
- After

## Nested models

## DataSourceInterface

## Custom annotations