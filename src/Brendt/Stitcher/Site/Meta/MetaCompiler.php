<?php

namespace Brendt\Stitcher\Site\Meta;

use Brendt\Stitcher\Site\Page;
use Pageon\Html\Meta\Meta;

class MetaCompiler
{
    /**
     * @var callable[]
     */
    private $compilers = [];
    private $metaConfig;

    public function __construct(array $metaConfig = [])
    {
        $this->metaConfig = $metaConfig;

        $this->addCompiler('title', [$this, 'compileTitle']);
        $this->addCompiler('description', [$this, 'compileDescription']);
        $this->addCompiler('image', [$this, 'compileImage']);
        $this->addCompiler('pagination', [$this, 'compilePagination']);
        $this->addCompiler('meta', [$this, 'compileMeta']);
        $this->addCompiler('_name', [$this, 'compileNamedMeta']);
    }

    public function setDefaultMeta(Meta &$meta)
    {
        foreach ($this->metaConfig as $name => $value) {
            $meta->name($name, $value);
        }
    }

    public function addCompiler(string $name, callable $callback) : MetaCompiler
    {
        $this->compilers[$name] = $callback;

        return $this;
    }

    public function compilePage(Page $page)
    {
        $variables = $page->getVariables();

        foreach ($variables as $name => $data) {
            if (!$page->isParsedVariable($name)) {
                continue;
            }

            $this->compilePageVariable($page, $name, $data);
        }
    }

    public function compilePageVariable(Page $page, string $name, $data, bool $force = false)
    {
        $isCustomCompiler = isset($this->compilers[$name]);

        if (!$isCustomCompiler && !$force) {
            return;
        } else if (!$isCustomCompiler) {
            $compileCallable = $this->compilers['_name'];
        } else {
            $compileCallable = $this->compilers[$name];
        }

        $compileCallable($page, $data, $name);
    }

    private function compileTitle(Page $page, string $data)
    {
        $page->getMeta()->title($data);
    }

    private function compileDescription(Page $page, string $data)
    {
        $page->getMeta()->description($data);
    }

    private function compileImage(Page $page, $data)
    {
        if (is_array($data) && isset($data['src'])) {
            $page->getMeta()->image($data['src']);
        } else if (is_string($data)) {
            $page->getMeta()->image($data);
        }
    }

    private function compilePagination(Page $page, array $pagination)
    {
        if (isset($pagination['next']['url'])) {
            $page->getMeta()->link('next', $pagination['next']['url']);
        }

        if (isset($pagination['prev']['url'])) {
            $page->getMeta()->link('prev', $pagination['prev']['url']);
        }
    }

    private function compileMeta(Page $page, array $data)
    {
        foreach ($data as $name => $item) {
            $this->compilePageVariable($page, $name, $item, true);
        }
    }

    private function compileNamedMeta(Page $page, $data, string $name)
    {
        $page->getMeta()->name($name, $data);
    }
}
