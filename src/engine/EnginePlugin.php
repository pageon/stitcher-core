<?php

namespace brendt\stitcher\engine;

use brendt\stitcher\Config;
use brendt\stitcher\factory\ProviderFactory;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class EnginePlugin {

    public function css($src, $isCritical = false) {
        /** @var ProviderFactory $factory */
        $factory = Config::getDependency('factory.provider');
        $provider = $factory->getProvider($src);
        $data = $provider->parse($src);

        if ($isCritical) {
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

    public function js($src) {
        // TODO
    }
    
}
