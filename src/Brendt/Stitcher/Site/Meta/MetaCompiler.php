<?php

namespace Brendt\Stitcher\Site\Meta;

use Brendt\Stitcher\Site\Page;

class MetaCompiler
{

    /**
     * @var callable[]
     */
    private $compilers = [];

    /**
     * MetaCompiler constructor.
     */
    public function __construct() {
        $this->addCompiler('title', [$this, 'compileTitle']);
        $this->addCompiler('description', [$this, 'compileDescription']);
        $this->addCompiler('image', [$this, 'compileImage']);
        $this->addCompiler('pagination', [$this, 'compilePagination']);
        $this->addCompiler('meta', [$this, 'compileMeta']);
        $this->addCompiler('_name', [$this, 'compileNamedMeta']);
    }

    /**
     * @param string   $name
     * @param callable $callback
     *
     * @return MetaCompiler
     */
    public function addCompiler(string $name, callable $callback) : MetaCompiler {
        $this->compilers[$name] = $callback;

        return $this;
    }

    /**
     * @param Page $page
     */
    public function compilePage(Page $page) {
        $variables = $page->getVariables();

        foreach ($variables as $name => $data) {
            if (!$page->isParsedVariable($name)) {
                continue;
            }

            $this->compilePageVariable($page, $name, $data);
        }
    }

    /**
     * @param Page   $page
     * @param string $name
     * @param mixed  $data
     * @param bool   $force
     */
    public function compilePageVariable(Page $page, string $name, $data, bool $force = false) {
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

    /**
     * @param Page   $page
     * @param string $data
     */
    private function compileTitle(Page $page, string $data) {
        $page->meta->title($data);
    }

    /**
     * @param Page   $page
     * @param string $data
     */
    private function compileDescription(Page $page, string $data) {
        $page->meta->description($data);
    }

    /**
     * @param Page $page
     * @param      $data
     */
    private function compileImage(Page $page, $data) {
        if (is_array($data) && isset($data['src'])) {
            $page->meta->image($data['src']);
        } else if (is_string($data)) {
            $page->meta->image($data);
        }
    }

    /**
     * @param Page  $page
     * @param array $pagination
     */
    private function compilePagination(Page $page, array $pagination) {
        if (isset($pagination['next']['url'])) {
            $page->meta->link('next', $pagination['next']['url']);
        }

        if (isset($pagination['prev']['url'])) {
            $page->meta->link('prev', $pagination['prev']['url']);
        }
    }

    /**
     * @param Page  $page
     * @param array $data
     */
    private function compileMeta(Page $page, array $data) {
        foreach ($data as $name => $item) {
            $this->compilePageVariable($page, $name, $item, true);
        }
    }

    /**
     * @param Page   $page
     * @param        $data
     * @param string $name
     */
    private function compileNamedMeta(Page $page, $data, string $name) {
        $page->meta->name($name, $data);
    }

}
