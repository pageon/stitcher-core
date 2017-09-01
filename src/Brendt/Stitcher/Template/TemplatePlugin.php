<?php

namespace Brendt\Stitcher\Template;

use Brendt\Stitcher\Lib\Browser;
use Pageon\Html\Meta\Meta;
use Brendt\Image\ResponsiveFactory;
use Brendt\Stitcher\Factory\ParserFactory;
use Brendt\Stitcher\Site\Http\Header;
use Brendt\Stitcher\Site\Page;
use CSSmin;
use JSMin;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This class provides functionality which can be used by template plugins/functions.
 */
class TemplatePlugin
{
    private $browser;
    private $parserFactory;
    private $responsiveFactory;
    private $cssMinifier;
    private $minify;
    /** @var Page */
    private $page;

    public function __construct(
        Browser $browser,
        ParserFactory $parserFactory,
        ResponsiveFactory $responsiveFactory,
        CSSmin $cssMinifier,
        bool $minify
    ) {
        $this->browser = $browser;
        $this->parserFactory = $parserFactory;
        $this->responsiveFactory = $responsiveFactory;
        $this->cssMinifier = $cssMinifier;
        $this->minify = $minify;
    }

    public function setPage(Page $page) : TemplatePlugin
    {
        $this->page = $page;

        return $this;
    }

    /**
     * This function will read meta configuration from `meta` and output the corresponding meta tags.
     *
     * @param array $extra
     *
     * @return string
     */
    public function meta(array $extra = []) : string
    {
        $meta = $this->page ? $this->page->getMeta() : new Meta();
        foreach ($extra as $name => $content) {
            $meta->name($name, $content);
        }

        return $meta->render();
    }

    /**
     * This function will take a source path and an optional inline parameter.
     * The CSS file will be copied from the source path to the public directory.
     * If the `minify` option is set to true in config.yml, the output will be minified.
     *
     * If the inline parameter is set, the output won't be copied to a public file,
     * but instead be outputted to an HTML string which can be included in a template.
     *
     * Files with the .scss and .sass extensions will be compiled to normal CSS files.
     *
     * @param string $src
     * @param bool   $inline Inline this resource
     * @param bool   $push   Use HTTP/2 server push to send this resource.
     *
     * @return string
     */
    public function css(string $src, bool $inline = false, bool $push = false) : string
    {
        $parser = $this->parserFactory->getByFileName($src);
        $data = $parser->parse($src);

        if ($this->minify) {
            $data = $this->cssMinifier->run($data);
        }

        if ($inline) {
            return "<style>{$data}</style>";
        }
        $srcParsed = preg_replace('/\.scss|\.sass/', '.css', $src);
        $fs = new Filesystem();
        $dst = "{$this->browser->getPublicDir()}/$srcParsed";

        if ($push) {
            $this->page->addHeader(Header::link("\"</{$srcParsed}>; rel=preload; as=style\""));
        }

        if ($fs->exists($dst)) {
            $fs->remove($dst);
        }

        $fs->dumpFile($dst, $data);

        return "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$srcParsed}\">";
    }

    /**
     * This function will take a source path and an optional inline parameter.
     * The JS file will be copied from the source path to the public directory.
     * If the `minify` option is set to true in config.yml, the output will be minified.
     *
     * If the inline parameter is set, the output won't be copied to a public file,
     * but instead be outputted to an HTML string which can be included in a template.
     *
     * @param string $src
     * @param bool   $inline Inline this resource
     * @param bool   $async
     * @param bool   $push   Use HTTP/2 server push to send this resource.
     *
     * @return string
     */
    public function js(string $src, bool $inline = false, bool $async = false, bool $push = false) : string
    {
        $parser = $this->parserFactory->getByFileName($src);
        $data = $parser->parse($src);

        if ($this->minify) {
            $data = JSMin::minify($data);
        }

        if ($inline) {
            return "<script>{$data}</script>";
        }

        $fs = new Filesystem();
        $dst = "{$this->browser->getPublicDir()}/$src";

        if ($push) {
            $this->page->addHeader(Header::link("\"</{$src}>; rel=preload; as=script\""));
        }

        if ($fs->exists($dst)) {
            $fs->remove($dst);
        }

        $fs->dumpFile($dst, $data);
        $result = "<script src=\"{$src}\"";

        if ($async) {
            $result .= ' async';
        }

        $result .= "></script>";

        return $result;
    }

    /**
     * Create a responsive image using brendt\responsive-images.
     *
     * @param      $src
     * @param bool $push Use HTTP/2 server push to send this resource.
     *
     * @return array
     *
     * @see \Brendt\Image\ResponsiveFactory
     */
    public function image(string $src, bool $push = false) : array
    {
        $image = $this->responsiveFactory->create($src);

        if (!$image) {
            return ['src' => null, 'srcset' => null, 'sizes' => null];
        }

        if ($push) {
            $this->page->addHeader(Header::link("\"</{$image->src()}>; rel=preload; as=image\""));
        }

        return [
            'src'    => $image->src(),
            'srcset' => $image->srcset(),
            'sizes'  => $image->sizes(),
        ];
    }

    /**
     * Create a public file from the src directory and return its path.
     *
     * @param string $src
     * @param bool   $push Use HTTP/2 server push to send this resource.
     *
     * @return null|string
     */
    public function file($src, bool $push = false)
    {
        $src = trim($src, '/');
        $files = $this->browser->src()->path($src)->files()->getIterator();
        $files->rewind();
        /** @var SplFileInfo $file */
        $file = $files->current();

        if (!$file) {
            return null;
        }

        $fs = new Filesystem();
        $dst = "{$this->browser->getPublicDir()}/{$src}";

        if ($push) {
            $this->page->addHeader(Header::link("\"</{$src}>; rel=preload; as=document\""));
        }

        if ($fs->exists($dst)) {
            $fs->remove($dst);
        }

        $fs->dumpFile($dst, $file->getContents());

        return "/{$src}";
    }

}
