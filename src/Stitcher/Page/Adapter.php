<?php

namespace Stitcher\Page;

interface Adapter
{
    public function transform(array $pageConfiguration): array;
}
