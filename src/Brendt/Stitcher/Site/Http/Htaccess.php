<?php

namespace Brendt\Stitcher\Site\Http;

use Brendt\Stitcher\Exception\ConfigurationException;
use Brendt\Stitcher\Site\Page;
use Symfony\Component\Filesystem\Filesystem;
use Tivie\HtaccessParser\HtaccessContainer;
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
     * @var array|\ArrayAccess|HtaccessContainer
     */
    private $contents;

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
    public function clearPageBlocks() : void {
        $headerBlock = $this->getHeaderBlock();

        foreach ($headerBlock as $content) {
            if ($content instanceof Block && strtolower($content->getName()) === 'filesmatch') {
                $headerBlock->removeChild($content);
            }
        }
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
