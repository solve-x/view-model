<?php

namespace SolveX\ViewModel;

use Carbon\Carbon;
use Mockery\Exception;
use ReflectionClass;
use ReflectionProperty;
use Illuminate\Contracts\Translation\Translator;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use SolveX\ViewModel\Annotations\Annotation;
use SolveX\ViewModel\Annotations\DataType;
use SolveX\ViewModel\Annotations\DefaultValue;
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
    protected $translator;

    /**
     * @var DataSourceInterface|null
     */
    protected $data;

    /**
     * ViewModel constructor.
     *
     * @param DataSourceInterface|null $data
     * @param Translator|null $translator
     * @throws \SolveX\ViewModel\ValidationException
     * @throws \ReflectionException
     * @throws \RuntimeException
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

        if (! $this->isValid()) {
            throw new ValidationException('Validation failed!', $this->errors);
        }
    }

    /**
     * Retrieves annotations for each property,
     * and processes those annotations.
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    protected function validateAndSetProperties()
    {
        //echo microtime()."START: ".get_class($this).PHP_EOL;
        $properties = $this->getProperties();

        $reader = new AnnotationReader();

        foreach ($properties as $property) {
            //echo get_class($this)."->".$property->getName().PHP_EOL;
            //echo "Validate and set: ".$property->getName().PHP_EOL;
            $this->validateAndSetProperty($property, $reader);
            //echo "End Validate and set: ".$property->getName().PHP_EOL;
        }
        //echo microtime()."STOP: ".get_class($this).PHP_EOL;
    }

    /**
     * @param ReflectionProperty $property
     * @param AnnotationReader $reader
     * @throws \RuntimeException
     */
    private function validateAndSetProperty($property, $reader)
    {
        $annotations = $reader->getPropertyAnnotations($property);

        $required = $this->containsAnnotation($annotations, Required::class);
        $hasDefault = $this->containsAnnotation($annotations, DefaultValue::class);
        $isBooleanType = $this->containsBoolTypeAnnotation($annotations);
        $present = $this->data->has($property->getName());

        $isMissingAndValid = !$present && !$hasDefault && !$required && !$isBooleanType;
        if ($isMissingAndValid) {
            return;
        }

        $propertyName = $property->getName();
        $isMissingAndIsRequired = !$present && !$hasDefault && $required;
        if ($isMissingAndIsRequired) {
            $this->isValid = false;
            $this->errors[$propertyName][] = "{$propertyName} is required and missing!";
            return;
        }

        // NOTE: this handles cases where unchecked checkboxes do not send
        // values back to the server
        $isAbsentBoolean = !$present && !$hasDefault && $isBooleanType;

        if ($isAbsentBoolean) {
            $value = 'false';
        } else if ($hasDefault && ! $present) {
            $value = $this->getDefaultValueFromAnnotation($annotations);
        } else {
            $value = $this->data->get($propertyName);
        }

        if (!is_string($value) && !is_array($value) && null !== $value) {
            $providedType = gettype($value);
            throw new \RuntimeException("The value must be a string or array, {$providedType} given!");
        }

        $this->processAnnotations(
            $annotations,
            $propertyName,
            $value
        );
    }

    /**
     * @param Annotation[] $annotations
     * @param string $propertyName
     * @param mixed $value
     * @throws \RuntimeException
     */
    protected function processAnnotations($annotations, $propertyName, $value)
    {
        $dataType = $this->getAnnotation($annotations, DataType::class);
        if (null === $dataType) {
            throw new \RuntimeException('Data type not found!');
        }

        $validationContext = new ValidationContext($this->data, $this->translator);
        if (DataType::isComplex($dataType)) {
            $this->validateAndSetComplex(
                $dataType->Type,
                $value,
                $validationContext,
                $this->{$propertyName},
                $this->errors[$propertyName]
            );
            return;
        }

        $this->validateAndSetSimple(
            $annotations,
            $propertyName,
            $value,
            $validationContext
        );
    }

    /**
     * @param string $type
     * @param array $value
     * @param ValidationContext $context
     * @param $instance
     * @param array $errors
     */
    protected function validateAndSetComplex($type, $value, $context, &$instance, &$errors)
    {
        try {
            $instance = new $type(
                new KeyValueDataSource($value),
                $context->getTranslator()
            );

            // Non-throwable view models are possible!
            if (! $instance->isValid()) {
                $errors = $instance->getErrors();
            }

        } catch (ValidationException $exception) {
            $errors = $exception->getErrors();
        }
    }

    /**
     * @param Annotation[] $annotations
     * @param string $propertyName
     * @param string $value
     * @param ValidationContext $validationContext
     */
    private function validateAndSetSimple($annotations, $propertyName, $value, $validationContext)
    {
        // Validate
        $validationSuccessful = true;
        foreach ($annotations as $annotation) {
            $valid = $this->processAnnotation(
                $annotation,
                $propertyName,
                $value,
                $validationContext
            );

            if (! $valid) {
                $validationSuccessful = false;
            }
        }

        // Transform if validation succeeds
        if (! $validationSuccessful) {
            $this->isValid = false;
            return;
        }

        foreach ($annotations as $annotation) {
            $value = $annotation->transform($value);
        }

        $this->{$propertyName} = $value;
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
            $errors = $validationResult->getErrors();
            foreach ($errors as $error) {
                $this->errors[$propertyName][] = $this->getErrorTranslation(
                    $error->getMessage(),
                    $error->getReplacements()
                );
            }
            return false;
        }
        return true;
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
     * @throws \ReflectionException
     */
    protected function getProperties()
    {
        $reflectionClass = new ReflectionClass(static::class);
        return $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    /**
     * @param Annotation[] $annotations
     * @return null|string
     */
    protected function getDefaultValueFromAnnotation($annotations)
    {
        $annotation = $this->getAnnotation($annotations, DefaultValue::class);

        return null === $annotation ? null : $annotation->value;
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
