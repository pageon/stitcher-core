<?php

namespace brendt\stitcher\engine;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class EnginePlugin {

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

    public function css($src, $inline = false) {
        /** @var ProviderFactory $factory */
        $factory = Config::getDependency('factory.provider');
        $provider = $factory->getProvider($src);
        $data = $provider->parse($src);

        if ($inline) {
            $result = "<style>{$data}</style>";
        } else {
            $publicDir = Config::get('directories.public');
            $srcParsed = preg_replace('/\.scss|\.sass/', '.css', $src);
            $fs = new Filesystem();
            $dst = "{$publicDir}/$srcParsed";

            if ($fs->exists($dst)) {
                $fs->remove($dst);
            }

            $fs->dumpFile($dst, $data);
            $result = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$srcParsed}\">";
        }

        return $result;
    }

    public function js($src, $inline = false) {
        /** @var ProviderFactory $factory */
        $factory = Config::getDependency('factory.provider');
        $provider = $factory->getProvider($src);
        $data = $provider->parse($src);

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
            $result = "<script src=\"{$src}\"></script>";
        }

        return $result;
    }
    
}
