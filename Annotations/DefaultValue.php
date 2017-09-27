<?php

namespace SolveX\ViewModel\Annotations;

/**
 * @Annotation
 */
class DefaultValue extends Annotation
{
    /**
     * @\Doctrine\Common\Annotations\Annotation\Required()
     * @var string
     */
    public $value;
}