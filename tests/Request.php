<?php

use SolveX\ViewModel\DataSourceInterface;

class Request implements DataSourceInterface
{
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

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