<?php

namespace Brendt\Stitcher\Parser;

use Brendt\Stitcher\Lib\Browser;
use Leafo\ScssPhp\Compiler;
use Symfony\Component\Finder\SplFileInfo;

/**
 * The SassParser take a path to one or more sass files, compiles it to CSS and returns that CSS.
 */
class SassParser implements Parser
{
    private $browser;
    private $sass;

    public function __construct(Browser $browser, Compiler $sass)
    {
        $this->sass = $sass;
        $this->browser = $browser;
        $this->sass->addImportPath("{$this->browser->getSrcDir()}/css");
    }

    public function parse($path)
    {
        /** @var SplFileInfo[] $files */
        $files = $this->browser->src()->path(trim($path, '/'))->files();
        $data = '';

        foreach ($files as $file) {
            $data .= $this->sass->compile($file->getContents());
        }

        return $data;
    }
}
