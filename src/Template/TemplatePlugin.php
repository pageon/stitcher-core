<?php

namespace Brendt\Stitcher\Template;

use Brendt\Image\ResponsiveFactory;
use Brendt\Stitcher\Config;
use Brendt\Stitcher\Factory\ParserFactory;
use CSSmin;
use JSMin;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * This class provides functionality which can be used by template plugins/functions.
 */
class TemplatePlugin
{

    /**
     * @var string
     */
    private $publicDir;

    /**
     * @var string
     */
    private $srcDir;

    /**
     * @var ParserFactory
     */
    private $parserFactory;

    /**
     * @var ResponsiveFactory
     */
    private $responsiveFactory;

    /**
     * @var CSSmin
     */
    private $cssMinifier;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var bool
     */
    private $minify;

    public function __construct(
        ParserFactory $parserFactory,
        ResponsiveFactory $responsiveFactory,
        CSSmin $cssMinifier,
        string $publicDir,
        string $srcDir,
        $minify,
        $meta
    ) {
        $this->parserFactory = $parserFactory;
        $this->responsiveFactory = $responsiveFactory;
        $this->cssMinifier = $cssMinifier;
        $this->publicDir = $publicDir;
        $this->srcDir = $srcDir;
        $this->minify = $minify;

        $this->meta = is_array($meta) ? $meta : [$meta];
    }

    /**
     * This function will read meta configuration from `meta` and output the corresponding meta tags.
     *
     * @return string
     */
    public function meta() {
        $result = [];

        foreach ($this->meta as $name => $content) {
            if (!is_string($content)) {
                continue;
            }

            $result[] = "<meta name=\"{$name}\" content=\"{$content}\">";
        }

        return implode("\n", $result);
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
     * @param bool   $inline
     *
     * @return string
     */
    public function css($src, $inline = false) {
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
        $dst = "{$this->publicDir}/$srcParsed";

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
     * @param bool   $inline
     * @param bool   $async
     *
     * @return string
     */
    public function js($src, $inline = false, $async = false) {
        $parser = $this->parserFactory->getByFileName($src);
        $data = $parser->parse($src);

        if ($this->minify) {
            $data = JSMin::minify($data);
        }

        if ($inline) {
            return "<script>{$data}</script>";
        }

        $fs = new Filesystem();
        $dst = "{$this->publicDir}/$src";

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
     * @param $src
     *
     * @return array
     *
     * @see \Brendt\Image\ResponsiveFactory
     */
    public function image($src) {
        $image = $this->responsiveFactory->create($src);

        if (!$image) {
            return ['src' => null, 'srcset' => null, 'sizes' => null];
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
     * @param $src
     *
     * @return null|string
     */
    public function file($src) {
        $src = trim($src, '/');
        $files = Finder::create()->in($this->srcDir)->path($src)->getIterator();
        $files->rewind();
        /** @var SplFileInfo $file */
        $file = $files->current();

        if (!$file) {
            return null;
        }

        $fs = new Filesystem();
        $dst = "{$this->publicDir}/{$src}";

        if ($fs->exists($dst)) {
            $fs->remove($dst);
        }

        $fs->dumpFile($dst, $file->getContents());

        return "/{$src}";
    }

}
