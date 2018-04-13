<?php

namespace Stitcher\Task;

use Pageon\Html\SiteMap;
use Stitcher\Task;

class RenderSiteMap implements Task
{
    /** @var string */
    private $publicDirectory;

    /** @var \Pageon\Html\SiteMap */
    private $siteMap;

    public function __construct(string $publicDirectory, SiteMap $siteMap)
    {
        $this->publicDirectory = $publicDirectory;
        $this->siteMap = $siteMap;
    }

    public function execute(): void
    {
        $siteMap = $this->siteMap->render();

        file_put_contents($this->publicDirectory . '/sitemap.xml', $siteMap);
    }
}
