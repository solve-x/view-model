<?php

namespace SolveX\ViewModel;

use RuntimeException;

/**
 * Returns true is the given array is considered associative.
 *
 * @param array $arr
 * @return bool
 */
function is_associative($arr)
{
    foreach ($arr as $key => $_) {
        if (is_string($key)) {
            return true;
        }
    }

    return false;
}

class KeyValueDataSource implements DataSourceInterface
{
    /**
     * @var array
     */
    private $data;

    /**
     * KeyValueDataSource constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine if the data source contains a non-empty value for a key.
     *
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Retrieve an item from the data source.
     *
     * @param string $key Lookup key.
     * @return string|array|KeyValueDataSource
     * @throws RuntimeException When $key is missing.
     */
    public function get($key)
    {
        $value = $this->data[$key];

        if (is_array($value) && is_associative($value)) {
            return new KeyValueDataSource($value);
        }

        return $value;
    }
}