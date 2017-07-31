<?php

namespace SolveX\ViewModel\DataAnnotations;

/**
 * @Annotation
 */
class MinLength extends Annotation
{
    public $Value;

    public function IsValid($value)
    {
        return strlen($value) >= $this->Value;
    }
}