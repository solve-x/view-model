<?php

namespace SolveX\ViewModel\DataAnnotations;

/**
 * @Annotation
 */
class Required extends Annotation
{
    public function IsValid($value)
    {
        return true;
    }
}