<?php

namespace Stitcher;

interface Configureable
{
    public function isValidConfiguration($subject): bool;
}
