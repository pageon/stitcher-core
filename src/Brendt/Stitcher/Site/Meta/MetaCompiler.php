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
        $this->addCompiler('title', function (Page $page, string $data) {
            $page->meta->title($data);
        });

        $this->addCompiler('description', function (Page $page, string $data) {
            $page->meta->description($data);
        });

        $this->addCompiler('image', function (Page $page, $data) {
            if (is_array($data) && isset($data['src'])) {
                $page->meta->image($data['src']);
            } else if (is_string($data)) {
                $page->meta->image($data);
            }
        });

        $this->addCompiler('pagination', function (Page $page, array $pagination) {
            if (isset($pagination['next']['url'])) {
                $page->meta->link('next', $pagination['next']['url']);
            }

            if (isset($pagination['prev']['url'])) {
                $page->meta->link('prev', $pagination['prev']['url']);
            }
        });

        $this->addCompiler('meta', function (Page $page, array $data) {
            foreach ($data as $name => $item) {
                $this->compilePageVariable($page, $name, $item, true);
            }
        });

        $this->addCompiler('_name', function (Page $page, $data, string $name) {
            $page->meta->name($name, $data);
        });
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
    public function compilePage(Page $page) : void {
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
    public function compilePageVariable(Page $page, string $name, $data, bool $force = false) : void {
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

}
