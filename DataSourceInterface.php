<?php

namespace SolveX\ViewModel;

use RuntimeException;

interface DataSourceInterface
{
    /**
     * Determine if the data source contains a non-empty value for a key.
     *
     * @param array|string $key
     * @return bool
     */
    public function has($key);

    /**
     * Retrieve an item from the data source.
     *
     * @param string $key Lookup key.
     * @param string|array|null $default Default when key not found.
     * @return string|array
     * @throws RuntimeException When $key is missing and $default is not provided.
     */
    public function get($key, $default = null);
}
