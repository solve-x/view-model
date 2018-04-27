<?php

namespace SolveX\ViewModel;

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
     * @param \ReflectionProperty $property
     */
    public function __construct($property)
    {
        $this->name = $property->getName();

        $this->processDocComment($property);
    }

    /**
     * @param \ReflectionProperty $property
     */
    protected function processDocComment($property)
    {
        $docComment = $property->getDocComment();

        $matched = preg_match('/@var\s+(\S+)/', $docComment, $matches);

        $type = $matched ? $matches[1] : null;

        $this->nullable = false;
        $this->isBooleanType = $type === 'boolean';

        if (strpos($type, '|') !== false) {
            $types = explode('|', $type);
            $this->nullable = in_array('null', $types, true);
            $this->isBooleanType = in_array('boolean', $types, true);

            foreach ($types as $t) {
                if ($t !== 'null') {
                    $type = $t;
                    break;
                }
            }
        }

        $this->dataType = $type;
    }

    public function getMappedValue($value)
    {
        if ($this->dataType === 'boolean') {
            return (
                $value === true ||
                $value === 'true' ||
                $value === 'on' ||
                $value === '1' ||
                $value === 1
            );
        }

        if ($this->dataType === 'int') {
            return (int) $value;
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
    public function isRequiredBoolean()
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
    public function hasComplexDataType()
    {
        return false; //is_subclass_of($this->dataType, DataType::class);
    }
}