<?php

namespace Stitcher\Integration;

use Stitcher\App;
use Stitcher\File;
use Stitcher\Task\Parse;
use Stitcher\Test\StitcherTest;
use Symfony\Component\Filesystem\Filesystem;

class StaticFilesTest extends StitcherTest
{
    /** @test */
    public function it_moves_static_files_and_directories_to_the_public_directory(): void
    {
        App::init();

        App::get(Parse::class)->execute();

        $fs = new Filesystem();

        $this->assertTrue($fs->exists(File::path('public/static/file.html')));
        $this->assertTrue($fs->exists(File::path('public/static/sub-dir/test.html')));
    }
}
