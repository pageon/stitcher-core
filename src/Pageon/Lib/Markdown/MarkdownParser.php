<?php

namespace Pageon\Lib\Markdown;

use Closure;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\Node\Inline\Link;
use League\CommonMark\MarkdownConverter;
use Pageon\Html\Image\Image;
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

        $environment
            ->addRenderer(Link::class, new ExternalLinkRenderer())
            ->addRenderer(Image::class, new ImageRenderer($imageFactory));

        foreach (self::$extensions as $closure) {
            $environment = $closure($environment);
        }

        $this->converter = new MarkdownConverter($environment);
    }

    public function parse(?string $markdown): ?string
    {
        if (! $markdown) {
            return null;
        }

        return $this->converter->convertToHtml($markdown);
    }
}
