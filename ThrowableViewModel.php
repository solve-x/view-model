<?php

namespace SolveX\ViewModel;

class ThrowableViewModel extends ViewModel
{
    public function __construct(DataSourceInterface $data = null)
    {
        parent::__construct($data);

        if (! $this->isValid()) {
            throw new ValidationException('Validation failed!');
        }
    }
}
