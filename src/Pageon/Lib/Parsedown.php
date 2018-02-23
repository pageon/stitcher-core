<?php

namespace Pageon\Lib;

use Pageon\Html\Image\ImageFactory;
use \Parsedown as LibParsedown;
use Stitcher\Exception\InvalidConfiguration;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

/**
 * This class adds extensions to the Parsedown library:
 *
 *  - Code block classes.
 *  - External links with `target="_blank"`.
 *  - Images are parsed with with the image factory
 *
 * Class Parsedown
 */
class Parsedown extends LibParsedown
{
    private $imageFactory;

    public function __construct(ImageFactory $imageFactory) {
        $this->imageFactory = $imageFactory;
    }

    protected function element(array $Element) {
        $markup = parent::element($Element);

        if (isset($Element['attributes']['href']) && strpos($Element['attributes']['href'], '*') === 0) {
            return $this->parseBlankLink($Element);
        }

        if (isset($Element['attributes']['srcset'])) {
            return $this->parseImageWithSrcset($Element);
        }

        return $markup;
    }

    protected function blockFencedCode($Line) {
        $block = parent::blockFencedCode($Line);

        if (isset($block['element']['text']['attributes']['class'])) {
            $block['element']['attributes']['class'] = $block['element']['text']['attributes']['class'];
        }

        return $block;
    }

    protected function inlineImage($Excerpt) {
        $Inline = parent::inlineImage($Excerpt);

        if (!isset($Inline['element']['attributes']['src'])) {
            return $Inline;
        }

        $src = $Inline['element']['attributes']['src'];

        try {
            $responsiveImage = $this->imageFactory->create($src);
        } catch (FileNotFoundException $e) {
            throw InvalidConfiguration::fileNotFound($src);
        }

        $Inline['element']['attributes']['srcset'] = $responsiveImage->srcset() ?? null;
        $Inline['element']['attributes']['sizes'] = $responsiveImage->sizes() ?? null;
        $Inline['element']['attributes']['alt'] = $Inline['element']['attributes']['alt'] ?? null;

        return $Inline;
    }

    private function parseBlankLink($Element): string {
        $href = $Element['attributes']['href'];
        $href = substr($href, 1);

        return "<a href='$href' target='_blank' rel='noreferrer noopener'>{$Element['text']}</a>";
    }

    private function parseImageWithSrcset($Element): string {
        $src = $Element['attributes']['src'];
        $srcset = $Element['attributes']['srcset'];
        $sizes = $Element['attributes']['sizes'];
        $alt = $Element['attributes']['alt'];

        return "<img src=\"{$src}\" srcset=\"{$srcset}\" sizes=\"{$sizes}\" alt=\"{$alt}\" />";
    }
}
