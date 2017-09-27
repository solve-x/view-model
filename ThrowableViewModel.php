<?php

namespace SolveX\ViewModel;

use Illuminate\Contracts\Translation\Translator;

/**
 * @deprecated This is the default behavior of the base view model.
 * @see NonThrowableViewModel
 */
class ThrowableViewModel extends ViewModel
{
    public function __construct(DataSourceInterface $data = null, Translator $translator = null)
    {
        parent::__construct($data, $translator);

        if (! $this->isValid()) {
            throw new ValidationException('Validation failed!');
        }
    }
}
