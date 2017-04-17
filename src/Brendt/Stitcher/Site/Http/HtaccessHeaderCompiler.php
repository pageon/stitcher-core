<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Site\Page;
use Tivie\HtaccessParser\Token\Directive;

class HtaccessHeaderCompiler implements HeaderCompiler
{
    /**
     * @var Htaccess
     */
    private $htaccess;

    /**
     * HtaccessHeaderCompiler constructor.
     *
     * @param Htaccess $htaccess
     */
    public function __construct(Htaccess $htaccess) {
        $this->htaccess = $htaccess;
    }

    /**
     * @param Page $page
     */
    public function compilePage(Page $page) : void {
        $headers = $page->getHeaders();
        if (!count($headers)) {
            return;
        }

        $pageBlock = $this->htaccess->getPageBlock($page);

        foreach ($headers as $header) {
            $directive = new Directive('Header');
            $directive->addArgument("add {$header->getHtaccessHeader()}");

            $pageBlock->addChild($directive);
        }
    }
}
