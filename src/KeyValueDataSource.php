<?php

namespace SolveX\ViewModel;

use RuntimeException;

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
     * @return string|array
     * @throws RuntimeException When $key is missing.
     */
    public function get($key)
    {
        return $this->data[$key];
    }
}