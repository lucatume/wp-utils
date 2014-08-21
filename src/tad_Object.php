<?php

class tad_Object
{
    /**
     * @var tad_FunctionsAdapter|tad_FunctionsAdapterInterface an instance of the global functions adapter.
     */
    protected $f;

    /**
     * Sets the functions adapter to be used for calling globally defined functions.
     *
     * @param tad_FunctionsAdapterInterface $functionsAdapter
     */
    public function setFunctionsAdapter(tad_FunctionsAdapterInterface $functionsAdapter = null)
    {
        $this->f = $functionsAdapter ? $functionsAdapter : new tad_FunctionsAdapter();
    }
}