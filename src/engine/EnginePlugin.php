<?php

namespace brendt\stitcher\engine;

use brendt\stitcher\Config;
use Symfony\Component\Finder\Finder;

class EnginePlugin {

    public function css($src, $isCritical = false) {
        $result = '';

        if ($isCritical) {
            $finder = new Finder();

            $files = $finder->files()->in(Config::get('directories.src'))->path(trim($src, '/'));

            $result .= '<style>';
            foreach ($files as $file) {
                $result .= $file->getContents();
            }
            $result .= '</style>';
        } else {
            $result = "<link rel=\"stylesheet\" type=\"text/css\" href=\"{$src}\">";
        }

        return $result;
    }

    public function js($src) {
        
    }
    
}
