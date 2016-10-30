<?php

use brendt\stitcher\factory\TemplateEngineFactory;
use brendt\stitcher\Config;
use brendt\stitcher\engine\smarty\SmartyEngine;
use brendt\stitcher\engine\twig\TwigEngine;

class TemplateEngineFactoryTest extends PHPUnit_Framework_TestCase {

    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    protected function createTemplateEngineFactory() {
        return new TemplateEngineFactory();
    }

    public function test_factory_smarty() {
        $factory = $this->createTemplateEngineFactory();

        $this->assertInstanceOf(SmartyEngine::class, $factory->getByType(TemplateEngineFactory::SMARTY_ENGINE));
    }

    public function test_factory_twig() {
        $factory = $this->createTemplateEngineFactory();

        $this->assertInstanceOf(TwigEngine::class, $factory->getByType(TemplateEngineFactory::TWIG_ENGINE));
    }

    /**
     * @expectedException brendt\stitcher\exception\UnknownEngineException
     */
    public function test_unknown_id_throws_exception() {
        $factory = $this->createTemplateEngineFactory();

        $factory->getByType('unknown');
    }

}
