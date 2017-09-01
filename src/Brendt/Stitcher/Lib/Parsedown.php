<?php

namespace Brendt\Stitcher\Lib;

use Brendt\Stitcher\Parser\ImageParser;
use \Parsedown as LibParsedown;

/**
 * This class adds two extensions to the Parsedown library:
 *
 *  - Code block classes.
 *  - External links with `target="_blank"`.
 *  - Images are parsed with with the image parser
 *
 * Class Parsedown
 * @package Brendt\Stitcher\Lib
 */
class Parsedown extends LibParsedown
{
    private $imageParser;

    public function __construct(ImageParser $imageParser)
    {
        $this->imageParser = $imageParser;
    }

    public function element(array $Element)
    {
        if (isset($Element['attributes']['href']) && strpos($Element['attributes']['href'], '*') === 0) {
            return $this->parseBlankLink($Element);
        }

        if (isset($Element['attributes']['srcset'])) {
            return $this->parseImageWithSrcset($Element);
        }

        return parent::element($Element);
    }

    protected function blockFencedCode($Line)
    {
        $block = parent::blockFencedCode($Line);

        if (isset($block['element']['text']['attributes']['class'])) {
            $block['element']['attributes']['class'] = $block['element']['text']['attributes']['class'];
        }

        return $block;
    }

    protected function inlineImage($Excerpt)
    {
        $Inline = parent::inlineImage($Excerpt);

        if (!isset($Inline['element']['attributes']['src'])) {
            return $Inline;
        }

        $src = $Inline['element']['attributes']['src'];

        $responsiveImage = $this->imageParser->parse($src);

        $Inline['element']['attributes']['srcset'] = $responsiveImage['srcset'] ?? null;
        $Inline['element']['attributes']['sizes'] = $responsiveImage['sizes'] ?? null;
        $Inline['element']['attributes']['alt'] = $Inline['element']['attributes']['alt'] ?? null;

        return $Inline;
    }

    private function parseBlankLink($Element) : string
    {
        $href = $Element['attributes']['href'];
        $href = substr($href, 1);

        return "<a href=\"$href\" target=\"_blank\" rel=\"noreferrer noopener\">{$Element['text']}</a>";
    }

    private function parseImageWithSrcset($Element) : string
    {
        $src = $Element['attributes']['src'];
        $srcset = $Element['attributes']['srcset'];
        $sizes = $Element['attributes']['sizes'];
        $alt = $Element['attributes']['alt'];

        return "<img src=\"{$src}\" srcset=\"{$srcset}\" sizes=\"{$sizes}\" alt=\"{$alt}\" />";
    }
}
