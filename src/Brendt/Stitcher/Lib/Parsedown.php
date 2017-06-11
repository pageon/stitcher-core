<?php

namespace Brendt\Stitcher\Lib;

use \Parsedown as LibParsedown;

class Parsedown extends LibParsedown
{
    protected function blockFencedCode($Line)
    {
        $block = parent::blockFencedCode($Line);

        if (isset($block['element']['text']['attributes']['class'])) {
            $block['element']['attributes']['class'] = $block['element']['text']['attributes']['class'];
        }

        return $block;
    }
    
    protected function inlineLink($Excerpt) {
        $block = parent::inlineLink($Excerpt);
        return $block;
    }
}
