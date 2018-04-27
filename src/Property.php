<?php

namespace SolveX\ViewModel;

use ReflectionProperty;
use RuntimeException;

/**
 * Class Property stores information about a ViewModel property,
 * like type and nullability.
 */
class Property
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $nullable;

    /**
     * @var bool
     */
    private $isBooleanType;

    /**
     * @var string
     */
    private $dataType;

    /**
     * Property constructor.
     *
     * @param ReflectionProperty $reflectionProperty
     */
    public function __construct($reflectionProperty)
    {
        $this->name = $reflectionProperty->getName();
        $this->processDocComment($reflectionProperty);
    }

    /**
     * @param ReflectionProperty $reflectionProperty
     */
    protected function processDocComment($reflectionProperty)
    {
        $docComment = $reflectionProperty->getDocComment();
        $matched = preg_match('/@var\s+(\S+)/', $docComment, $matches);
        $type = $matched ? $matches[1] : null;
        $this->nullable = false;
        $this->isBooleanType = $type === 'boolean';

        if (strpos($type, '|') !== false) {
            $types = explode('|', $type);
            $this->nullable = in_array('null', $types, true);
            $this->isBooleanType = in_array('boolean', $types, true) || in_array('bool', $types, true);

            foreach ($types as $t) {
                if ($t !== 'null') {
                    $type = $t;
                    break;
                }
            }
        }

        $this->dataType = $type;
    }

    /**
     * @param $value
     * @return string|bool|int|ViewModel
     * @throws ViewModelException
     * @throws RuntimeException
     */
    public function getMappedValue($value)
    {
        if ($this->isBooleanType) {
            return (
                $value === true ||
                $value === 'true' ||
                $value === 'on' ||
                $value === '1' ||
                $value === 1
            );
        }

        if ($this->dataType === 'int' || $this->dataType === 'integer') {
            return (int) $value;
        }

        if ($this->isNestedViewModel()) {
            return new $this->dataType($value);
        }

        return $value;
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
    public function isNullable()
    {
        return $this->nullable;
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
    public function isNonNullableBoolean()
    {
        return $this->isBooleanType && ! $this->nullable;
    }

    /**
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * @return bool
     */
    public function isNestedViewModel()
    {
        $basicTypes = [
            'bool',
            'boolean',
            'string',
            'int',
            'integer',
            'float',
            'double',
            'array',
        ];

        return ! in_array($this->dataType, $basicTypes, true);
    }
}