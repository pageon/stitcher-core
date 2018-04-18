<?php

namespace Pageon\Html\Meta;

interface MetaItem
{
    /**
     * @return string
     */
    public function render(array $extra = []) : string;
}
