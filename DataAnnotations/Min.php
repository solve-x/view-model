<?php

namespace SolveX\ViewModel\DataAnnotations;

/**
 * @Annotation
 */
class Min extends Annotation
{
    public $Value;

    public function IsValid($value)
    {
        return $value >= $this->Value;
    }
}