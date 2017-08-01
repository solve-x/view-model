<?php

namespace SolveX\ViewModel;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionProperty;
use SolveX\ViewModel\ValidationAnnotations\Required;

class ViewModel
{
    public $IsValid = false;

    public function __construct(DataSourceInterface $data = null)
    {
        // $data is null when a viewmodel class (extending this one) was
        // instantiated manually with new MyViewModel().
        // In this case we do nothing.
        if ($data === null) {
            return;
        }

        AnnotationRegistry::registerLoader([$this, '__autoload']); // TODO: figure out if this loader is necessary!

        $this->IsValid = true;
        $properties = $this->getProperties();
        $this->setAndValidateProperties($data, $properties);
    }

    /**
     * Internal autoloader for annotations.
     *
     * @param string $class
     * @return bool
     */
    public function __autoload($class)
    {
        $namespace = 'SolveX\ViewModel\ValidationAnnotations';

        if (strpos($class, $namespace) === 0) {
            $className = substr($class, strlen($namespace) + 1);
            $file = __DIR__ . '/ValidationAnnotations/' . $className . '.php';
            if (is_file($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }

    /**
     * Uses reflection to retrieve properties of the extended class.
     *
     * @return ReflectionProperty[]
     */
    protected function getProperties()
    {
        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->getProperties();
    }

    /**
     * @param DataSourceInterface $data
     * @param ReflectionProperty[] $properties
     */
    protected function setAndValidateProperties(DataSourceInterface $data, $properties)
    {
        $reader = new AnnotationReader();

        foreach ($properties as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            $required = $this->containsRequiredAnnotation($annotations);
            $present = $data->has($property->getName());

            if (! $present) {
                if ($required) {
                    $this->IsValid = false;
                }

                continue;
            }

            $this->processAnnotations($annotations, $property, $data);
        }
    }

    /**
     * Returns true when ValidationAnnotations\Required is among given $annotations.
     *
     * @param ValidationAnnotations\ValidationAnnotation[] $annotations
     * @return bool
     */
    protected function containsRequiredAnnotation($annotations)
    {
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Required) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ValidationAnnotations\ValidationAnnotation[] $annotations
     * @param ReflectionProperty $property
     * @param DataSourceInterface $data
     */
    protected function processAnnotations($annotations, $property, DataSourceInterface $data)
    {
        $validationSuccessful = true;

        foreach ($annotations as $annotation) {
            $validationSuccessful = $validationSuccessful && $this->processAnnotation($annotation, $property, $data);
        }

        if ($validationSuccessful) {
            $propertyName = $property->getName();
            $this->{$propertyName} = $data->get($propertyName);
        }
    }

    /**
     * @param ValidationAnnotations\ValidationAnnotation $annotation
     * @param ReflectionProperty $property
     * @param DataSourceInterface $data
     * @return bool
     */
    protected function processAnnotation($annotation, $property, DataSourceInterface $data)
    {
        $value = $data->get($property->getName());
        $validationResult = $annotation->validate($value, $data, $property);

        if (! $validationResult->isOk()) {
            $this->IsValid = false;
            return false;
        }

        return true;
    }
}
