<?php

class tad_Object
{
    protected $f;

    public function setFunctionsAdapter(tad_FunctionsAdapterInterface $functionsAdapter = null)
    {
        $this->f = $functionsAdapter ? $functionsAdapter : new tad_FunctionsAdapter();
    }
}