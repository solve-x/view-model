<?php

namespace SolveX\ViewModel;

use Illuminate\Translation\Translator;

class ValidationContext
{
    /**
     * @var DataSourceInterface
     */
    private $data;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * ValidationContext constructor.
     *
     * @param DataSourceInterface $data
     * @param Translator|null $translator
     */
    public function __construct(DataSourceInterface $data, Translator $translator = null)
    {
        $this->data = $data;
        $this->translator = $translator;
    }

    /**
     * @return DataSourceInterface
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }
}
