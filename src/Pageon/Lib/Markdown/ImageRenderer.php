<?php

namespace Pageon\Lib\Markdown;

use InvalidArgumentException;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Text;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use Pageon\Html\Image\ImageFactory;
use Stitcher\Exception\InvalidConfiguration;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImageRenderer implements InlineRendererInterface
{
    /** @var \Pageon\Html\Image\ImageFactory */
    private $imageFactory;

    public function __construct(ImageFactory $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (! $inline instanceof Image) {
            throw new InvalidArgumentException('Inline must be instance of ' . Image::class);
        }

        $attributes = [];

        $src = $inline->getUrl();

        try {
            $responsiveImage = $this->imageFactory->create($src);
        } catch (FileNotFoundException $e) {
            throw InvalidConfiguration::fileNotFound($src);
        }

        $alt = $inline->firstChild();

        $attributes['src'] = $src;
        $attributes['srcset'] = $responsiveImage->srcset() ?? null;
        $attributes['sizes'] = $responsiveImage->sizes() ?? null;
        $attributes['alt'] = $alt instanceof Text
            ? $alt->getContent()
            : '';

        return new HtmlElement(
            'img',
            $attributes,
            $htmlRenderer->renderInlines($inline->children())
        );
    }
}
