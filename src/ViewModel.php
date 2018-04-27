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
     * A flag that indicates whether or not validation passed.
     *
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var DataSourceInterface|null
     */
    protected $data;

    /**
     * ViewModel constructor.
     *
     * @param DataSourceInterface|null $data
     * @throws \SolveX\ViewModel\ValidationException
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
        $this->isValid = true;

        $this->validateAndSetProperties();

        // TODO: optional validation (illuminate/validation)
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
     * Retrieves annotations for each property, and processes those annotations.
     *
     * @throws \ReflectionException
     * @throws \RuntimeException
     */
    protected function validateAndSetProperties()
    {
        $reader = new AnnotationReader();
        foreach ($this->getPropertiesByReflection() as $reflectionProperty) {
            $property = $this->createProperty($reader, $reflectionProperty);
            $shouldContinue = $this->propertyValueMissingAndInvalid($property);
            if (! $shouldContinue) {
                continue;
            }
            $value = $this->determinePropertyValue($property);
            $this->assertPropertyAndValueOk($property, $value);
            $this->applyAnnotations($property, $value);
        }
    }

    /**
     * @param AnnotationReader $reader
     * @param ReflectionProperty $reflectionProperty
     * @return Property
     */
    protected function createProperty(AnnotationReader $reader, ReflectionProperty $reflectionProperty)
    {
        $annotations = $reader->getPropertyAnnotations($reflectionProperty);
        return new Property($reflectionProperty, $annotations, $this->data);
    }

    /**
     * @param Property $propertyInfo
     * @throws \RuntimeException
     */
    protected function validateAndSetProperty($propertyInfo)
    {


//        if (DataType::isComplex($dataType)) {
//            $this->validateAndSetComplex(
//                $dataType->Type,
//                $value,
//                $validationContext,
//                $this->{$propertyName},
//                $this->errors[$propertyName]
//            );
//            return;
//        }

//        $this->validateAndSetSimple(
//            $annotations,
//            $propertyName,
//            $value,
//            $validationContext
//        );
    }

    /**
     * @param Property $property
     * @return bool
     */
    protected function propertyValueMissingAndInvalid(Property $property)
    {
        if ($property->isMissingAndValid()) {
            return false;
        }

        if ($property->isMissing() && $property->isRequired()) {
            $name = $property->getName();
            $this->errors[$name][] = "{$name} is required and missing!";
            $this->isValid = false;
            return false;
        }

        return true;
    }

    /**
     * @param Property $property
     * @param string $value
     * @throws \RuntimeException
     */
    protected function assertPropertyAndValueOk(Property $property, $value)
    {
        $this->assertPropertyHasDataType($property);
        $this->assertValueType($value);
    }

    /**
     * @param $value
     * @throws \RuntimeException
     */
    protected function assertValueType($value)
    {
        if (! is_string($value) && ! is_array($value) && null !== $value) {
            $providedType = gettype($value);
            throw new \RuntimeException(
                "The value provided by the DataSourceInterface must be a string or array, {$providedType} given!"
            );
        }
    }

    /**
     * @param Property $property
     * @throws \RuntimeException
     */
    protected function assertPropertyHasDataType(Property $property)
    {
        if (null === $property->getDataType()) {
            throw new \RuntimeException('Data type annotation not found!');
        }
    }

    /**
     * @param Property $property
     * @return array|null|string
     * @throws \RuntimeException
     */
    protected function determinePropertyValue(Property $property)
    {
        // NOTE: this handles cases where unchecked checkboxes do not send
        // values back to the server
        if ($property->isAbsentBoolean()) {
            return 'false';
        }

        if ($property->isAbsentWithDefault()) {
            return $property->getDefaultValue();
        }

        return $this->data->get($property->getName());
    }

    /**
     * @param Property $property
     * @param $value
     */
    protected function applyAnnotations(Property $property, $value)
    {
        $validationSuccessful = true;
        foreach ($property->getAnnotations() as $annotation) {
            $valid = $this->processAnnotation($annotation, $property->getName(), $value);
            if (! $valid) {
                $validationSuccessful = false;
            }
        }

        if (! $validationSuccessful) {
            $this->isValid = false;
            return;
        }

        $value = $this->transformValue($property, $value);

        $this->{$property->getName()} = $value;
    }

    /**
     * @param string $type
     * @param array $value
     * @param ValidationContext $context
     * @param $instance
     * @param array $errors
     */
  /*  protected function validateAndSetComplex($type, $value, $context, &$instance, &$errors)
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
            $instance = $exception->getModel();
        }
    }*/

    /**
     * Runs the validation of a particular annotation.
     *
     * @param Annotation $annotation
     * @param string $propertyName
     * @param mixed $value
     * @return bool
     */
    protected function processAnnotation($annotation, $propertyName, $value)
    {
        $context = new ValidationContext($this->data, $this->translator);
        $validationResult = $annotation->validate($value, $context);

        if ($validationResult->isOk()) {
            return true;
        }

        $errors = $validationResult->getErrors();
        foreach ($errors as $error) {
            $this->errors[$propertyName][] = $this->getErrorTranslation(
                $error->getMessage(),
                $error->getReplacements()
            );
        }

        return false;
    }

    protected function processNestedDataTypeAnnotation()
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
            $instance = $exception->getModel();
        }
    }

    /**
     * @param Property $property
     * @param $value
     * @return mixed
     */
    protected function transformValue(Property $property, $value)
    {
        foreach ($property->getAnnotations() as $annotation) {
            $value = $annotation->transform($value);
        }

        return $value;
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
