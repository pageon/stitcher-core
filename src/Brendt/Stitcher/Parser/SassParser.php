<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Config;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The SassParser take a path to one or more sass files, compiles it to CSS and returns that CSS.
 */
class SassParser implements Parser
{

    /**
     * @var Compiler
     */
    private $sass;

    /**
     * @var string
     */
    private $srcDir;

    /**
     * SassParser constructor.
     *
     * @param Compiler $sass
     * @param string   $srcDir
     */
    public function __construct(Compiler $sass, string $srcDir) {
        $this->sass = $sass;
        $this->srcDir = $srcDir;
    }

    /**
     * @param $path
     *
     * @return string
     */
    public function parse($path) {
        /** @var SplFileInfo[] $files */
        $files = Finder::create()->files()->in($this->srcDir)->path(trim($path, '/'));
        $data = '';

        foreach ($files as $file) {
            $data .= $this->sass->compile($file->getContents());
        }

        return $data;
    }

}
