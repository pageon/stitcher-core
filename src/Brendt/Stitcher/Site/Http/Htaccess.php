<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Lib\Browser;
use Brendt\Stitcher\Site\Page;
use Symfony\Component\Filesystem\Filesystem;
use Tivie\HtaccessParser\HtaccessContainer;
use Tivie\HtaccessParser\Parser;
use Tivie\HtaccessParser\Token\Block;
use Tivie\HtaccessParser\Token\Directive;
use Tivie\HtaccessParser\Token\WhiteLine;

class Htaccess
{
    private $fs;
    private $parser;
    /**
     * @var array|\ArrayAccess|HtaccessContainer
     */
    private $contents;
    private $redirectHttps = false;
    private $redirectWww = false;
    private $redirects = [];

    public function __construct(Browser $browser)
    {
        $path = "{$browser->getPublicDir()}/.htaccess";
        $this->fs = new Filesystem();

        if (!$this->fs->exists($path)) {
            $samplePath = __DIR__ . '/../../../../.htaccess';
            $this->fs->dumpFile($path, file_get_contents($samplePath));
        }

        $this->parser = new Parser(new \SplFileObject($path));
        $this->parser->ignoreWhitelines(false);
        $this->contents = $this->parser->parse();
    }

    public function setRedirectHttps(bool $redirectHttps = false) : Htaccess
    {
        $this->redirectHttps = $redirectHttps;

        return $this;
    }

    public function setRedirectWww(bool $redirectWww = false) : Htaccess
    {
        $this->redirectWww = $redirectWww;

        return $this;
    }

    public function addRedirect(string $from, string $to) : Htaccess
    {
        $this->redirects[$from] = $to;

        return $this;
    }

    public function parse() : string
    {
        $this->setupIndex();
        $this->clearRewriteBlock();

        if ($this->redirectWww) {
            $this->rewriteWww();
        }

        if ($this->redirectHttps) {
            $this->rewriteHttps();
        }

        $this->rewriteCustomRedirects();
        $this->rewriteHtml();

        return (string) $this->contents;
    }

    /**
     * Get or create the headers block
     *
     * @return Block
     */
    public function &getHeaderBlock() : Block
    {
        $headerBlock = $this->findHeaderBlockByModName('mod_headers.c');

        if (!$headerBlock) {
            $headerBlock = new Block('ifmodule');
            $headerBlock->addArgument('mod_headers.c');

            if ($this->contents instanceof HtaccessContainer) {
                $this->contents->append($headerBlock);
            }
        }

        return $headerBlock;
    }

    /**
     * Get or create a page block within the headers block
     *
     * @param Page $page
     *
     * @return Block
     */
    public function &getPageBlock(Page $page) : Block
    {
        $headerBlock = $this->getHeaderBlock();
        $pageId = trim($page->getId(), '/') ?? 'index';
        $pageId = pathinfo($pageId !== '' ? "{$pageId}" : 'index', PATHINFO_BASENAME);
        $pageName = '"^' . $pageId . '\.html$"';

        $pageBlock = $this->findPageBlockByParentAndName($headerBlock, $pageName);

        if (!$pageBlock) {
            $pageBlock = new Block('filesmatch');
            $pageBlock->addArgument($pageName);
            $headerBlock->addChild($pageBlock);
        }

        return $pageBlock;
    }

    /**
     * Get or create the rewrite block
     *
     * @return Block
     */
    public function &getRewriteBlock() : Block
    {
        $rewriteBlock = $this->findHeaderBlockByModName('mod_rewrite.c');

        if (!$rewriteBlock) {
            $rewriteBlock = new Block('ifmodule');
            $rewriteBlock->addArgument('mod_rewrite.c');

            if ($this->contents instanceof HtaccessContainer) {
                $this->contents->append($rewriteBlock);
            }
        }

        return $rewriteBlock;
    }

    public function clearPageBlocks()
    {
        $headerBlock = $this->getHeaderBlock();

        foreach ($headerBlock as $content) {
            if ($content instanceof Block && strtolower($content->getName()) === 'filesmatch') {
                $headerBlock->removeChild($content);
            }
        }
    }

    public function clearRewriteBlock()
    {
        $rewriteBlock = $this->getRewriteBlock();

        foreach ($rewriteBlock as $content) {
            if (
                $content instanceof WhiteLine ||
                ($content instanceof Directive && $content->getName() !== 'RewriteEngine' && $content->getName() !== 'DirectorySlash')
            ) {
                $rewriteBlock->removeChild($content);
            }
        }
    }

    private function findPageBlockByParentAndName(Block $headerBlock, string $pageName)
    {
        foreach ($headerBlock as $content) {
            $arguments = $content->getArguments();

            if (reset($arguments) === $pageName) {
                return $content;
            }
        }

        return null;
    }

    private function setupIndex()
    {
        foreach ($this->contents as $content) {
            if ($content instanceof Directive && $content->getName() === 'Options') {
                return;
            }
        }

        $this->contents[] = new Directive('Options -Indexes');
    }

    private function rewriteWww()
    {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{HTTP_HOST} !^www\.', '^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]');
    }

    private function rewriteHttps()
    {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{HTTPS} off', '(.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]');
    }

    private function rewriteCustomRedirects()
    {
        $rewriteBlock = $this->getRewriteBlock();
        $rewriteBlock->addLineBreak(1);

        foreach ($this->redirects as $from => $to) {
            $ruleLine = new Directive();
            $ruleLine->setName("RedirectMatch 301 ^{$from}$ {$to}");
            $rewriteBlock->addChild($ruleLine);
        }
    }

    private function rewriteHtml()
    {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{DOCUMENT_ROOT}/$1.html -f', '^(.+?)/?$ /$1.html [L]');
    }

    private function createConditionalRewrite(Block &$rewriteBlock, string $condition, string $rule)
    {
        $rewriteBlock->addLineBreak(1);

        $conditionLine = new Directive();
        $conditionLine->setName("RewriteCond {$condition}");
        $rewriteBlock->addChild($conditionLine);

        $ruleLine = new Directive();
        $ruleLine->setName("RewriteRule {$rule}");
        $rewriteBlock->addChild($ruleLine);
    }

    private function findHeaderBlockByModName(string $modName)
    {
        foreach ($this->contents as $content) {
            $arguments = $content->getArguments();
            if (reset($arguments) === $modName) {
                return $content;
            }
        }

        return null;
    }
}
