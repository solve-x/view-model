<?php

namespace SolveX\ViewModel;

use SolveX\ViewModel\Annotations\Annotation;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\Annotations\DefaultValue;
use SolveX\ViewModel\Annotations\Required;

class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $required;

    /**
     * @var bool
     */
    private $hasDefault;

    /**
     * @var bool
     */
    private $isBooleanType;

    /**
     * @var bool
     */
    private $present;

    /**
     * @var string|null
     */
    private $defaultValue;

    /**
     * @var Annotation[]
     */
    private $annotations;

    /**
     * @var DataType|null
     */
    private $dataType;

    /**
     * Property constructor.
     *
     * @param \ReflectionProperty $property
     * @param Annotation[] $annotations
     * @param DataSourceInterface $data
     */
    public function __construct($property, $annotations, DataSourceInterface $data)
    {
        $this->name = $property->getName();
        $this->annotations = $annotations;
        $this->required = $this->containsAnnotation($annotations, Required::class);
        $this->hasDefault = $this->containsAnnotation($annotations, DefaultValue::class);
        $this->isBooleanType = $this->containsBoolTypeAnnotation($annotations);
        $this->present = $data->has($property->getName());
        if (!$this->present && $this->hasDefault) {
            $this->defaultValue = $this->getDefaultValueFromAnnotation($annotations);
        }
        $this->dataType = $this->getAnnotation($annotations, DataType::class);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @return bool
     */
    public function hasDefault()
    {
        return $this->hasDefault;
    }

    /**
     * @return bool
     */
    public function isBooleanType()
    {
        return $this->isBooleanType;
    }

    /**
     * @return bool
     */
    public function isPresent()
    {
        return $this->present;
    }

    /**
     * @return bool
     */
    public function isMissing()
    {
        return !$this->present && !$this->hasDefault;
    }

    /**
     * @return bool
     */
    public function isMissingAndValid()
    {
        return $this->isMissing() && !$this->required && !$this->isBooleanType;
    }

    /**
     * @return bool
     */
    public function isAbsentBoolean()
    {
        return !$this->present && !$this->hasDefault && $this->isBooleanType;
    }

    /**
     * @return bool
     */
    public function isAbsentWithDefault()
    {
        return !$this->present && $this->hasDefault;
    }

    /**
     * @return null|string
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return null|DataType
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return bool
     */
    public function hasComplexDataType()
    {
        return is_subclass_of($this->dataType, DataType::class);
    }

    /**
     * @return Annotation[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * @param Annotation[] $annotations
     * @return null|string
     */
    protected function getDefaultValueFromAnnotation($annotations)
    {
        /** @var DefaultValue $annotation */
        $annotation = $this->getAnnotation($annotations, DefaultValue::class);

        return null === $annotation ? null : $annotation->value;
    }

    /**
     * Returns true when Annotations\DataType Bool is among given $annotations.
     *
     * @param Annotation[] $annotations
     * @return bool
     */
    protected function containsBoolTypeAnnotation($annotations)
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof DataType &&
                DataType::Bool === $annotation->Type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Annotation[] $annotations
     * @param string $className
     * @return bool
     */
    protected function containsAnnotation($annotations, $className)
    {
        return null !== $this->getAnnotation($annotations, $className);
    }

    /**
     * @param Annotation[] $annotations
     * @param string $className
     * @return null|Annotation|DataType
     */
    protected function getAnnotation($annotations, $className)
    {
        foreach ($annotations as $annotation) {
            if (is_a($annotation, $className)) {
                return $annotation;
            }
        }

        return null;
    }
}