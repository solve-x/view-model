<?php

namespace SolveX\ViewModel;

use Illuminate\Contracts\Translation\Translator;

class NonThrowableViewModel extends ViewModel
{
    public function __construct(DataSourceInterface $data = null, Translator $translator = null)
    {
        try {
            parent::__construct($data, $translator);
        } catch (ValidationException $e) {
            // Expected exception
        }
    }
}