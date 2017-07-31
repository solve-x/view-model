<?php

namespace SolveX\ViewModel\DataAnnotations;

abstract class Annotation
{
    abstract public function IsValid($value);
}