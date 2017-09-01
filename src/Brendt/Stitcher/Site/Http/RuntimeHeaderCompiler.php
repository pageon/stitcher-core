<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Site\Page;

class RuntimeHeaderCompiler implements HeaderCompiler
{
    public function compilePage(Page $page)
    {
        foreach ($page->getHeaders() as $header) {
            header((string) $header);
        }
    }
}
