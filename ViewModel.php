<?php

namespace SolveX\ViewModel;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use ReflectionClass;
use ReflectionProperty;
use SolveX\ViewModel\Annotations\Annotation;
use SolveX\ViewModel\Annotations\Required;

/**
 * See ViewModel::registerAnnotationAutoloader().
 * This function is used because "static" variables
 * have their own identities in extended classes.
 */
function register_annotation_autoloader_once()
{
    static $autoloaderRegistered = false;

    if (! $autoloaderRegistered) {
        AnnotationRegistry::registerLoader('class_exists');
        $autoloaderRegistered = true;
    }
}

/**
 * Class ViewModel.
 *
 * <code>
 * </code>
 */
class ViewModel
{
    /**
     * A flag that indicates whether or not validation was passed.
     *
     * @var bool
     */
    public $IsValid = false;

    /**
     * An associative array (mapping property names to arrays of errors)
     * that is filled if validation failed.
     *
     * @var array
     */
    public $Errors = [];

    /**
     * ViewModel constructor.
     *
     * @param DataSourceInterface|null $data
     */
    public function __construct(DataSourceInterface $data = null)
    {
        // $data is null when a viewmodel class (extending this one) was
        // instantiated manually with new MyViewModel().
        // In this case we do nothing.
        // This is useful for testing.
        if ($data === null) {
            return;
        }

        $this->registerAnnotationAutoloader();

        $this->IsValid = true;
        $properties = $this->getProperties();
        $this->validateAndSetProperties($data, $properties);
    }

    /**
     * Doctrine Annotations library uses its own autoloading mechanism.
     * Here we use a suggestion from the library's source code and just pass
     * the autoloading to class_exists built-in function which results in composer doing
     * the actual loading.
     *
     * Registration is done only once.
     *
     * See
     * http://docs.doctrine-project.org/projects/doctrine-common/en/latest/reference/annotations.html#introduction
     * and
     * https://tinyurl.com/y83wvpcx
     */
    protected function registerAnnotationAutoloader()
    {
        register_annotation_autoloader_once();
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
     * Retrieves annotations for each property,
     * and processes those annotations.
     *
     * @param DataSourceInterface $data
     * @param ReflectionProperty[] $properties
     */
    protected function validateAndSetProperties(DataSourceInterface $data, $properties)
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
     * Returns true when Annotations\Required is among given $annotations.
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
            if (! $this->processAnnotation($annotation, $propertyName, $value, $validationContext)) {
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
     * Runs the validation of a particular annotation.
     *
     * @param Annotation $annotation
     * @param string $propertyName
     * @param mixed $value
     * @param ValidationContext $context
     * @return bool
     */
    protected function processAnnotation($annotation, $propertyName, $value, ValidationContext $context)
    {
        $validationResult = $annotation->validate($value, $context);

        if (! $validationResult->isOk()) {
            $this->Errors[$propertyName][] = $validationResult->getError();
            $this->IsValid = false;
            return false;
        }

        return true;
    }
}
