<?php

namespace SolveX\ViewModel;

use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;

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
     * @throws RuntimeException
     * @throws ReflectionException
     * @throws ViewModelException
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
     * @throws RuntimeException
     * @throws ViewModelException
     * @throws ReflectionException
     */
    protected function setProperties()
    {
        foreach ($this->getPropertiesByReflection() as $reflectionProperty) {
            $property = new Property($reflectionProperty);
            $propertyName = $property->getName();

            if (! $this->data->has($propertyName)) {
                if ($property->isNonNullableBoolean()) {
                    $this->{$propertyName} = false;
                    continue;
                }

                if (! $property->isNullable()) {
                    throw new ViewModelException("Expected value missing for property {$propertyName}!");
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
     * @throws ReflectionException
     */
    protected function getPropertiesByReflection()
    {
        return (new ReflectionClass(static::class))->getProperties(ReflectionProperty::IS_PUBLIC);
    }
}
