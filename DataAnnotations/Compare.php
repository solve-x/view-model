<?php

namespace SolveX\ViewModel\DataAnnotations;

/**
 * @Annotation
 */
class Compare extends Annotation
{
    public $Property;

    public function IsValid($value)
    {
        return true;
    }
}