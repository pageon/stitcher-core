<?php

namespace Pageon\Lib\Markdown;

use InvalidArgumentException;
use League\CommonMark\Extension\CommonMark\Node\Inline\Image;
use League\CommonMark\Node\Inline\Text;
use League\CommonMark\Node\Node;
use League\CommonMark\Renderer\ChildNodeRendererInterface;
use League\CommonMark\Renderer\NodeRendererInterface;
use League\CommonMark\Util\HtmlElement;
use Pageon\Html\Image\ImageFactory;
use Stitcher\Exception\InvalidConfiguration;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class ImageRenderer implements NodeRendererInterface
{
    /** @var \Pageon\Html\Image\ImageFactory */
    private $imageFactory;

    public function __construct(ImageFactory $imageFactory)
    {
        $this->imageFactory = $imageFactory;
    }

    public function render(Node $node, ChildNodeRendererInterface $childRenderer)
    {
        if (! $node instanceof Image) {
            throw new InvalidArgumentException('Inline must be instance of ' . Image::class);
        }

        $attributes = [];

        $src = $node->getUrl();

        try {
            $responsiveImage = $this->imageFactory->create($src);
        } catch (FileNotFoundException $e) {
            throw InvalidConfiguration::fileNotFound($src);
        }

        $alt = $node->firstChild();

        $attributes['src'] = $src;
        $attributes['srcset'] = $responsiveImage->srcset() ?? '';
        $attributes['sizes'] = $responsiveImage->sizes() ?? '';
        $attributes['alt'] = $alt instanceof Text
            ? $alt->getLiteral()
            : '';

        return new HtmlElement(
            'img',
            $attributes,
            $childRenderer->renderNodes($node->children())
        );
    }
}
