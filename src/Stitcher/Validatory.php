<?php

namespace Stitcher;

interface Validatory
{
    public function isValid($subject): bool;
}
