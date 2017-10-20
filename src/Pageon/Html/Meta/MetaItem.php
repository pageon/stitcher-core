<?php

namespace Pageon\Html\Meta;

interface MetaItem
{
    /**
     * @return string
     */
    public function render() : string;
}
