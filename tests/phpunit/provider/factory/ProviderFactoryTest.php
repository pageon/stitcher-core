<?php

use brendt\stitcher\provider\factory\ProviderFactory;
use brendt\stitcher\provider\FolderProvider;
use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\provider\MarkdownProvider;
use brendt\stitcher\provider\YamlProvider;

class ProviderFactoryTest extends PHPUnit_Framework_TestCase {

    protected function createProviderFactory() {
        return new ProviderFactory('./tests/src');
    }

    public function test_provider_factory_folder() {
        $factory = $this->createProviderFactory();

        $this->assertInstanceOf(FolderProvider::class, $factory->getProvider('churches/'));
        $this->assertInstanceOf(FolderProvider::class, $factory->getByType(ProviderFactory::FOLDER_PROVIDER));
    }

    public function test_provider_factory_json() {
        $factory = $this->createProviderFactory();

        $this->assertInstanceOf(JsonProvider::class, $factory->getProvider('churches.json'));
        $this->assertInstanceOf(JsonProvider::class, $factory->getByType(ProviderFactory::JSON_PROVIDER));
    }

    public function test_provider_factory_yaml() {
        $factory = $this->createProviderFactory();

        $this->assertInstanceOf(YamlProvider::class, $factory->getProvider('churches.yml'));
        $this->assertInstanceOf(YamlProvider::class, $factory->getByType(ProviderFactory::YAML_PROVIDER));
    }

    public function test_provider_factory_markdown() {
        $factory = $this->createProviderFactory();

        $this->assertInstanceOf(MarkdownProvider::class, $factory->getProvider('churches.md'));
        $this->assertInstanceOf(MarkdownProvider::class, $factory->getByType(ProviderFactory::MARKDOWN_PROVIDER));
    }

}
