<?php

namespace Stitcher\Task;

use Pageon\Config;
use Stitcher\App;
use Stitcher\File;
use Stitcher\Page\PageRenderer;
use Symfony\Component\Filesystem\Filesystem;

class RenderPage
{
    public static function execute($path, $pageId, $page)
    {
        File::base($path);

        App::init();

        $pageRenderer = App::get(PageRenderer::class);

        $publicDirectory = Config::get('publicDirectory');

        $fileName = $pageId === '/' ? 'index' : $pageId;

        $renderedPage = $pageRenderer->render($page);

        $fs = new Filesystem();

        $fs->dumpFile("{$publicDirectory}/{$fileName}.html", $renderedPage);
    }
}
