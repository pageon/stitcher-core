<?php

namespace Brendt\Stitcher\Site\Http;

class RuntimeHeaderCompiler implements HeaderCompiler
{
    public function compile(array $headers) {
        foreach ($headers as $name => $content) {
            header("{$name}: {$content}");
        }
    }
}
