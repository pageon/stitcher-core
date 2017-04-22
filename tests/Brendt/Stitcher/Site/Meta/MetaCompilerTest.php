<?php

namespace Brendt\Stitcher\Site\Meta;

use Brendt\Stitcher\Site\Page;
use PHPUnit\Framework\TestCase;

class MetaCompilerTest extends TestCase
{

    private function createPage(array $variables = []) : Page {
        $page = new Page('test', [
            'template'  => 'test',
            'variables' => $variables,
        ]);

        foreach ($page->getVariables() as $name => $value) {
            $page->setVariableIsParsed($name);
        }

        return $page;
    }

    public function test_general_page_compiling() {
        $compiler = new MetaCompiler();
        $page = $this->createPage([
            'title'       => 'A',
            'description' => 'B',
            'image'       => 'C',
            'extra'       => 'extra',
        ]);

        $compiler->compilePage($page);
        $meta = $page->meta->render();

        $this->assertContains('name="title" content="A"', $meta);
        $this->assertContains('name="description" content="B"', $meta);
        $this->assertContains('name="image" content="C"', $meta);
        $this->assertNotContains('extra', $meta);
    }

    public function test_page_compiling_with_meta_variable() {
        $compiler = new MetaCompiler();
        $page = $this->createPage([
            'title'       => 'A',
            'description' => 'B',
            'image'       => 'C',
            'meta'        => [
                'title' => 'AA',
                'extra' => 'extra',
            ],
        ]);

        $compiler->compilePage($page);
        $meta = $page->meta->render();

        $this->assertContains('name="title" content="AA"', $meta);
        $this->assertContains('name="description" content="B"', $meta);
        $this->assertContains('name="image" content="C"', $meta);
        $this->assertContains('name="extra" content="extra"', $meta);
    }

    public function test_page_compiling_with_pagination() {
        $compiler = new MetaCompiler();
        $page = $this->createPage([
            'pagination' => [
                'next' => [
                    'url' => 'ABC',
                ],
                'prev' => [
                    'url' => 'DEF',
                ],
            ],
        ]);

        $compiler->compilePage($page);
        $meta = $page->meta->render();

        $this->assertContains('rel="next" href="ABC"', $meta);
        $this->assertContains('rel="prev" href="DEF"', $meta);
    }

}
