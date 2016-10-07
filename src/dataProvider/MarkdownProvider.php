<?php

namespace brendt\stitcher\dataProvider;

use Michelf\Markdown;
use Symfony\Component\Finder\Finder;

class MarkdownProvider extends FileProvider {

    public function parse($path = '*.md') {
        $finder = new Finder();
        $files = $finder->files()->in("{$this->root}/data")->name($path);
        $data = [];

        foreach ($files as $file) {
            $data[] = Markdown::defaultTransform($file->getContents());
        }

        return $data;
    }

}
