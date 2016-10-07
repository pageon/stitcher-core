<?php

namespace brendt\stitcher\dataProvider;

use Michelf\Markdown;
use Symfony\Component\Finder\Finder;

class MarkdownProvider extends FileProvider {

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
        $files = $finder->files()->in("{$this->root}/data")->name($path);

        foreach ($files as $file) {
            $html = Markdown::defaultTransform($file->getContents());

            break;
        }

        return $html;
    }

}
