<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Site\Page;
use Symfony\Component\Filesystem\Filesystem;
use Tivie\HtaccessParser\HtaccessContainer;
use Tivie\HtaccessParser\Parser;
use Tivie\HtaccessParser\Token\Block;
use Tivie\HtaccessParser\Token\Directive;
use Tivie\HtaccessParser\Token\WhiteLine;

class Htaccess
{
    /**
     * @var Filesystem
     */
    private $fs;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var array|\ArrayAccess|HtaccessContainer
     */
    private $contents;

    /**
     * @var bool
     */
    private $redirectHttps = false;

    /**
     * @var bool
     */
    private $redirectWww = false;

    /**
     * @var array
     */
    private $redirects = [];

    /**
     * Htaccess constructor.
     *
     * @param string $path
     *
     * @throws ConfigurationException
     */
    public function __construct(string $path) {
        $this->fs = new Filesystem();

        if (!$this->fs->exists($path)) {
            $this->fs->dumpFile($path, file_get_contents(__DIR__ . '/../../../../.htaccess'));
        }

        $this->parser = new Parser(new \SplFileObject($path));
        $this->parser->ignoreWhitelines(false);
        $this->contents = $this->parser->parse();
    }

    /**
     * @param bool $redirectHttps
     *
     * @return Htaccess
     */
    public function setRedirectHttps(bool $redirectHttps = false) : Htaccess {
        $this->redirectHttps = $redirectHttps;

        return $this;
    }

    /**
     * @param bool $redirectWww
     *
     * @return Htaccess
     */
    public function setRedirectWww(bool $redirectWww = false) : Htaccess {
        $this->redirectWww = $redirectWww;

        return $this;
    }

    /**
     * Add custom redirects handles by htaccess
     *
     * @param string $from
     * @param string $to
     *
     * @return Htaccess
     */
    public function addRedirect(string $from, string $to) : Htaccess {
        $this->redirects[$from] = $to;

        return $this;
    }

    /**
     * Parse the modified .htaccess
     *
     * @return string
     */
    public function parse() : string {
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
    public function &getHeaderBlock() : Block {
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
    public function &getPageBlock(Page $page) : Block {
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
     * Clear all page header blocks
     */
    public function clearPageBlocks() {
        $headerBlock = $this->getHeaderBlock();

        foreach ($headerBlock as $content) {
            if ($content instanceof Block && strtolower($content->getName()) === 'filesmatch') {
                $headerBlock->removeChild($content);
            }
        }
    }

    /**
     * Clear all rewrites
     */
    public function clearRewriteBlock() {
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

    /**
     * Get or create the rewrite block
     *
     * @return Block
     */
    public function &getRewriteBlock() : Block {
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

    /**
     * @param Block  $headerBlock
     * @param string $pageName
     *
     * @return null|Block
     */
    private function findPageBlockByParentAndName(Block $headerBlock, string $pageName) {
        foreach ($headerBlock as $content) {
            $arguments = $content->getArguments();

            if (reset($arguments) === $pageName) {
                return $content;
            }
        }

        return null;
    }

    /**
     * Add www rewrite
     */
    private function rewriteWww() {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{HTTP_HOST} !^www\.', '^(.*)$ http://www.%{HTTP_HOST}/$1 [R=301,L]');
    }

    /**
     * Add https rewrite
     */
    private function rewriteHttps() {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{HTTPS} off', '(.*) https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]');
    }

    /**
     * Add custom rewrites
     */
    private function rewriteCustomRedirects() {
        $rewriteBlock = $this->getRewriteBlock();
        $rewriteBlock->addLineBreak(1);

        foreach ($this->redirects as $from => $to) {
            $ruleLine = new Directive();
            $ruleLine->setName("RedirectMatch 301 ^{$from}$ {$to}");
            $rewriteBlock->addChild($ruleLine);
        }
    }

    /**
     * Add .html rewrite
     */
    private function rewriteHtml() {
        $rewriteBlock = $this->getRewriteBlock();

        $this->createConditionalRewrite($rewriteBlock, '%{DOCUMENT_ROOT}/$1.html -f', '^(.+?)/?$ /$1.html [L]');
    }

    /**
     * @param Block  $rewriteBlock
     * @param string $condition
     * @param string $rule
     */
    private function createConditionalRewrite(Block &$rewriteBlock, string $condition, string $rule) {
        $rewriteBlock->addLineBreak(1);

        $conditionLine = new Directive();
        $conditionLine->setName("RewriteCond {$condition}");
        $rewriteBlock->addChild($conditionLine);

        $ruleLine = new Directive();
        $ruleLine->setName("RewriteRule {$rule}");
        $rewriteBlock->addChild($ruleLine);
    }

    /**
     * @param string $modName
     *
     * @return null|Block
     */
    private function findHeaderBlockByModName(string $modName) {
        foreach ($this->contents as $content) {
            $arguments = $content->getArguments();
            if (reset($arguments) === $modName) {
                return $content;
            }
        }

        return null;
    }
}
