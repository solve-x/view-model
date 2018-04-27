<?php

namespace SolveX\ViewModel;

use ReflectionClass;
use ReflectionProperty;

/**
 * Class ViewModel.
 */
class ViewModel
{
    /**
     * @var DataSourceInterface|null
     */
    protected $data;

    /**
     * ViewModel constructor.
     *
     * @param DataSourceInterface|null $data
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    public function __construct(DataSourceInterface $data = null)
    {
        // $data is null when a viewmodel class (extending this one) was
        // instantiated manually with new MyViewModel().
        // In this case we do nothing. This is useful for testing.
        if ($data === null) {
            return;
        }

        $this->data = $data;

        $this->setProperties();

        // TODO: optional validation (illuminate/validation)
    }

    /**
     * Maps source data to properties of the extended class.
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    protected function setProperties()
    {
        foreach ($this->getPropertiesByReflection() as $reflectionProperty) {
            $property = new Property($reflectionProperty);
            $propertyName = $property->getName();
            $valuePresent = $this->data->has($propertyName);

            if (! $valuePresent) {
                if ($property->isRequiredBoolean()) {
                    $this->{$propertyName} = false;
                }
                continue;
            }

            $value = $this->data->get($propertyName);
            $this->{$propertyName} = $property->getMappedValue($value);
        }
    }

    /**
     * Uses reflection to retrieve public properties of the extended class.
     *
     * @return ReflectionProperty[]
     * @throws \ReflectionException
     */
    protected function getPropertiesByReflection()
    {
        return (new ReflectionClass(static::class))->getProperties(ReflectionProperty::IS_PUBLIC);
    }
}
