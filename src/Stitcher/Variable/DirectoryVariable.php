<?php

namespace Stitcher\Variable;

use Stitcher\File;

class DirectoryVariable extends AbstractVariable
{
    /** @var \Stitcher\Variable\VariableParser */
    private $parser;

    public function __construct(string $path, VariableParser $parser)
    {
        parent::__construct($path);

        $this->parser = $parser;
    }

    public function parse(): AbstractVariable
    {
        $path = File::path($this->unparsed);

        $files = @scandir($path) ?: [];

        unset($files[0], $files[1]);

        $this->parsed = [];

        foreach ($files as $file) {
            $id = pathinfo($file, PATHINFO_FILENAME);

            $filePath = $path . $file;

            $this->parsed[$id] = [
                'id' => $id,
                'content' => $this->parser->parse($filePath),
            ];
        }

        return $this;
    }
}
