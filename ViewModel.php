<?php

namespace SolveX\ViewModel;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;

class ViewModel
{
    public $IsValid = false;

    public function __construct(DataSourceInterface $data)
    {
        AnnotationRegistry::registerLoader([$this, '__autoload']);

        $reflectionClass = new ReflectionClass(static::class);
        $properties = $reflectionClass->getProperties();
        $reader = new AnnotationReader();

        $this->IsValid = true;

        foreach ($properties as $property) {
            $propertyName = $property->getName();
            $annotations = $reader->getPropertyAnnotations($property);

            foreach ($annotations as $annotation) {
                /** @var DataAnnotations\Annotation $annotation */
                if (! $annotation->IsValid($data->get($propertyName))) {
                    $this->IsValid = false;
                } else {
                    $this->{$propertyName} = $data->get($propertyName);
                }
            }
        }
    }

    /**
     * Internal autoloader for annotations.
     *
     * @param string $class
     * @return bool
     */
    public function __autoload($class)
    {
        $namespace = 'SolveX\ViewModel\DataAnnotations';

        if (strpos($class, $namespace) === 0) {
            $className = substr($class, strlen($namespace) + 1);
            $file = __DIR__ . '/DataAnnotations/' . $className . '.php';
            if (is_file($file)) {
                require_once $file;
                return true;
            }
        }

        return false;
    }
}
