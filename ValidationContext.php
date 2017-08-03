<?php

namespace SolveX\ViewModel;

class ValidationContext
{
    /**
     * @var DataSourceInterface
     */
    private $data;

    /**
     * ValidationContext constructor.
     *
     * @param DataSourceInterface $data
     */
    public function __construct(DataSourceInterface $data)
    {
        $this->data = $data;
    }

    /**
     * @return DataSourceInterface
     */
    public function getData()
    {
        return $this->data;
    }
}