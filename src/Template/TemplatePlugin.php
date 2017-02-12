<?php

namespace Brendt\Stitcher\Template;

use brendt\image\ResponsiveFactory;
use Brendt\Stitcher\Config;
use Brendt\Stitcher\Factory\ParserFactory;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This class provides functionality which can be used by template plugins/functions.
 */
class TemplatePlugin
{

    /**
     * This function will read meta configuration from `meta` and output the corresponding meta tags.
     *
     * @return string
     */
    public function meta() {
        $meta = Config::get('meta');

        if (!is_array($meta)) {
            $meta = [$meta];
        }

        $result = [];

        foreach ($meta as $name => $content) {
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
        /** @var ParserFactory $factory */
        $factory = Config::getDependency('factory.parser');

        $parser = $factory->getParser($src);
        $data = $parser->parse($src);

        if (Config::get('minify')) {
            /** @var \CSSmin $minifier */
            $minifier = Config::getDependency('engine.minify.css');
            $data = $minifier->run($data);
        }

        if ($inline) {
            return "<style>{$data}</style>";
        }


        $publicDir = Config::get('directories.public');
        $srcParsed = preg_replace('/\.scss|\.sass/', '.css', $src);
        $fs = new Filesystem();
        $dst = "{$publicDir}/$srcParsed";

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
        /** @var ParserFactory $factory */
        $factory = Config::getDependency('factory.parser');

        $parser = $factory->getParser($src);
        $data = $parser->parse($src);

        if (Config::get('minify')) {
            $data = \JSMin::minify($data);
        }

        if ($inline) {
            $result = "<script>{$data}</script>";
        } else {
            $publicDir = Config::get('directories.public');
            $fs = new Filesystem();
            $dst = "{$publicDir}/$src";

            if ($fs->exists($dst)) {
                $fs->remove($dst);
            }

            $fs->dumpFile($dst, $data);
            $result = "<script src=\"{$src}\"";

            if ($async) {
                $result .= ' async';
            }

            $result .= "></script>";
        }

        return $result;
    }

    /**
     * Create a responsive image using brendt\responsive-images.
     *
     * @param $src
     *
     * @return array
     *
     * @see \brendt\image\ResponsiveFactory
     */
    public function image($src) {
        /** @var ResponsiveFactory $factory */
        $factory = Config::getDependency('factory.image');
        $image = $factory->create($src);

        if (!$image) {
            return ['src' => null, 'srcset' => null, 'sizes' => null];
        }

        return [
            'src'    => $image->src(),
            'srcset' => $image->srcset(),
            'sizes'  => $image->sizes(),
        ];
    }

}
