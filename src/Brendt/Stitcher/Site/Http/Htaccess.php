<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Site\Page;
use Symfony\Component\Filesystem\Filesystem;
use Tivie\HtaccessParser\Parser;
use Tivie\HtaccessParser\Token\Block;

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
     * @var array
     */
    private $contents;

    /**
     * Htaccess constructor.
     *
     * @param string $path
     *
     * @throws ConfigurationException
     */
    function __construct(string $path) {
        $this->fs = new Filesystem();

        if (!$this->fs->exists($path)) {
            $this->fs->dumpFile($path, file_get_contents(__DIR__ . '/../../../../.htaccess'));
        }

        $this->parser = new Parser(new \SplFileObject($path));
        $this->parser->ignoreWhitelines(false);
        $this->contents = $this->parser->parse();
    }

    /**
     * Parse the modified .htaccess
     *
     * @return string
     */
    public function parse() : string {
        return (string) $this->contents;
    }

    /**
     * Get or create the headers block
     *
     * @return Block
     */
    public function &getHeaderBlock() : Block {
        $headerBlock = null;

        foreach ($this->contents as $content) {
            if ($content instanceof Block
                && strtolower($content->getName()) === 'ifmodule'
                && count($content->getArguments())
                && $content->getArguments()[0] === 'mod_headers.c'
            ) {
                $headerBlock = $content;

                break;
            }
        }

        if (!$headerBlock) {
            $headerBlock = new Block('ifmodule');
            $headerBlock->addArgument('mod_headers.c');
            $this->contents->append($headerBlock);
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
        $pageBlock = null;
        $headerBlock = $this->getHeaderBlock();
        $pageId = trim($page->getId(), '/') ?? 'index';
        $pageId = pathinfo($pageId !== '' ? "{$pageId}" : 'index', PATHINFO_BASENAME);
        $pageName = '"^' . $pageId . '\.html$"';

        foreach ($headerBlock as $content) {
            if ($content instanceof Block
                && strtolower($content->getName()) === 'filesmatch'
                && count($content->getArguments())
                && $content->getArguments()[0] === $pageName
            ) {
                $pageBlock = $content;

                break;
            }
        }

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
    public function clearPageBlocks() : void {
        $pageBlock = null;
        $headerBlock = $this->getHeaderBlock();

        foreach ($headerBlock as $content) {
            if ($content instanceof Block && strtolower($content->getName()) === 'filesmatch') {
                $headerBlock->removeChild($content);
            }
        }
    }
}
