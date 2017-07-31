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
     * @param array|string $key
     * @return bool
     */
    public function has($key)
    {
        $keys = is_array($key) ? $key : [$key];

        foreach ($keys as $k) {
            if (! isset($this->data[$k])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieve an item from the data source.
     *
     * @param string $key Lookup key.
     * @param string|array|null $default Default when key not found.
     * @return string|array
     * @throws RuntimeException When $key is missing and $default is not provided.
     */
    public function get($key, $default = null)
    {
        if ($this->has($key)) {
            return $this->data[$key];
        }

        if ($default !== null) {
            return $default;
        }

        throw new RuntimeException('Invalid key!');
    }
}