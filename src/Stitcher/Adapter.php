<?php

namespace Stitcher;

interface Adapter
{
    public function transform(array $pageConfiguration): array;
}
