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

    protected function element(array $Element) {
        $markup = parent::element($Element);

        if (!isset($Element['attributes']['href'])) {
            return $markup;
        }

        $href = $Element['attributes']['href'];
        
        if (strpos($href, '*') !== 0) {
            return $markup;
        }

        $href = substr($href, 1);
        return "<a href='$href' target='_blank' rel='noreferrer noopener'>{$Element['text']}</a>";
    }
}
