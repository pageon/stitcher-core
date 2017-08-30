<?php

namespace Brendt\Stitcher\Lib;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class CdnTest extends TestCase
{
    public function test_cdn_parser() {
        $browser = new Browser('./tests/src', './tests/public', './tests/src/template', './tests/.cache');
        $cdn = new Cdn($browser, [
            'lib/lib.js',
            'lib/img/logo.png',
        ], true);

        $cdn->save();

        $fs = new Filesystem();
        $this->assertTrue($fs->exists('./tests/public/lib/lib.js'));
        $this->assertTrue($fs->exists('./tests/public/lib/img/logo.png'));
    }
}
