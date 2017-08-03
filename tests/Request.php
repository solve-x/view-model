<?php

use SolveX\ViewModel\DataSourceInterface;

class Request implements DataSourceInterface
{
    private $data = [];

    public function __construct($data)
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
        return isset($this->data[$key]);
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
        if ($this->has($key)) {
            return $this->data[$key];
        }

        throw new RuntimeException('Invalid key!');
    }
}