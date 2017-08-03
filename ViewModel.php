<?php

namespace SolveX\ViewModel;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionProperty;
use SolveX\ViewModel\Annotations\Annotation;
use SolveX\ViewModel\Annotations\Required;

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
        $namespace = 'SolveX\ViewModel\Annotations';

        if (strpos($class, $namespace) === 0) {
            $className = substr($class, strlen($namespace) + 1);
            $file = __DIR__ . '/Annotations/' . $className . '.php';
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
     * @param Annotation[] $annotations
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
     * @param Annotation[] $annotations
     * @param ReflectionProperty $property
     * @param DataSourceInterface $data
     */
    protected function processAnnotations($annotations, $property, DataSourceInterface $data)
    {
        $validationContext = new ValidationContext($data);
        $propertyName = $property->getName();
        $value = $data->get($propertyName);

        $validationSuccessful = true;

        foreach ($annotations as $annotation) {
            if (! $this->processAnnotation($annotation, $value, $validationContext)) {
                $validationSuccessful = false;
            }
        }

        // If all annotations successfully validated
        // the value being processed, we continue with the step 2:
        // potential value transform (e.g. casting to int).
        // Finally, we set the property value.
        if ($validationSuccessful) {
            foreach ($annotations as $annotation) {
                $value = $annotation->transform($value);
            }

            $this->{$propertyName} = $value;
        }
    }

    /**
     * @param Annotation $annotation
     * @param mixed $value
     * @param ValidationContext $context
     * @return bool
     */
    protected function processAnnotation($annotation, $value, ValidationContext $context)
    {
        $validationResult = $annotation->validate($value, $context);

        if (! $validationResult->isOk()) {
            $this->IsValid = false;
            return false;
        }

        return true;
    }
}
