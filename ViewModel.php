<?php

namespace SolveX\ViewModel;

use ReflectionClass;
use ReflectionProperty;
use Illuminate\Contracts\Translation\Translator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
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
 */
class ViewModel
{
    /**
     * A flag that indicates whether or not validation passed.
     *
     * @var bool
     */
    protected $isValid = false;

    /**
     * An associative array (mapping property names to arrays of errors)
     * that is filled if validation failed.
     *
     * @var array
     */
    protected $errors = [];

    /**
     * @var Translator|null
     */
    protected $translator = null;

    /**
     * @var DataSourceInterface|null
     */
    protected $data = null;

    /**
     * ViewModel constructor.
     *
     * @param DataSourceInterface|null $data
     * @param Translator|null $translator
     */
    public function __construct(DataSourceInterface $data = null, Translator $translator = null)
    {
        // $data is null when a viewmodel class (extending this one) was
        // instantiated manually with new MyViewModel().
        // In this case we do nothing.
        // This is useful for testing.
        if ($data === null) {
            return;
        }

        $this->translator = $translator;
        $this->data = $data;
        $this->isValid = true;

        $this->registerAnnotationAutoloader();
        $this->validateAndSetProperties();
    }

    /**
     * Returns true if validation succeeded.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * Returns an associative array of errors.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
     */
    protected function validateAndSetProperties()
    {
        $properties = $this->getProperties();
        $reader = new AnnotationReader();

        foreach ($properties as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            $required = $this->containsRequiredAnnotation($annotations);
            $present = $this->data->has($property->getName());

            if (! $present) {
                if ($required) {
                    $this->isValid = false;
                }

                continue;
            }

            $this->processAnnotations($annotations, $property);
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
     */
    protected function processAnnotations($annotations, $property)
    {
        $validationContext = new ValidationContext($this->data);
        $propertyName = $property->getName();
        $value = $this->data->get($propertyName);

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
            list($error, $replacements) = $validationResult->getErrorWithReplacements();
            $this->errors[$propertyName][] = $this->getErrorTranslation($error, $replacements);
            $this->isValid = false;
            return false;
        }

        return true;
    }

    /**
     * Translates validation error.
     *
     * @param string $error
     * @param array $replacements
     * @return string
     */
    protected function getErrorTranslation($error, $replacements)
    {
        if ($this->translator === null) {
            return $error;
        }

        return $this->translator->trans($error, $replacements);
    }
}
