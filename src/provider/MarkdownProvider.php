<?php

namespace brendt\stitcher\provider;

use Parsedown;
use Symfony\Component\Finder\Finder;

class MarkdownProvider extends AbstractProvider {

    /**
     * @param string $path
     *
     * @return string
     */
    public function parse($path = '*.md') {
        $html = '';
        $finder = new Finder();
        if (!strpos($path, '.md')) {
            $path .= '.md';
        }
        $files = $finder->files()->in("{$this->root}")->path($path);

        foreach ($files as $file) {
            $html = Parsedown::instance()->parse($file->getContents());

            break;
        }

        return $html;
    }

}
