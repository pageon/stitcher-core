<?php

namespace Pageon\Lib\Markdown;

use Closure;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment;
use League\CommonMark\Inline\Element\Image;
use League\CommonMark\Inline\Element\Link;
use Pageon\Html\Image\ImageFactory;

/**
 *  - Code block classes.
 *  - External links with `target="_blank"`.
 *  - Images are parsed with with the image factory
 */
class MarkdownParser
{
    /** @var \Closure[] */
    private static $extensions = [];

    /** @var \League\CommonMark\CommonMarkConverter */
    private $converter;

    public static function extension(Closure $closure): void
    {
        self::$extensions[] = $closure;
    }

    public function __construct(ImageFactory $imageFactory)
    {
        $environment = Environment::createCommonMarkEnvironment();

        foreach (self::$extensions as $closure) {
            $environment = $closure($environment);
        }

        $environment
            ->addInlineRenderer(Link::class, new ExternalLinkRenderer())
            ->addInlineRenderer(Image::class, new ImageRenderer($imageFactory));

        $this->converter = new CommonMarkConverter([], $environment);
    }

    public function parse(string $markdown): string
    {
        return $this->converter->convertToHtml($markdown);
    }
}
