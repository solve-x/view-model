<?php

namespace SolveX\ViewModel;

use RuntimeException;

interface DataSourceInterface
{
    /**
     * Determine if the data source contains a non-empty value for a key.
     *
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * Retrieve an item from the data source.
     *
     * @param string $key Lookup key.
     * @return string|array
     * @throws RuntimeException When $key is missing.
     */
    public function get($key);
}
