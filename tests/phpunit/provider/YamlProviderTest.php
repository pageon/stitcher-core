<?php

use brendt\stitcher\provider\YamlProvider;
use brendt\stitcher\exception\ProviderException;
use brendt\stitcher\Config;

class YamlProviderTest extends PHPUnit_Framework_TestCase {

    /**
     * YamlProviderTest constructor.
     */
    public function __construct() {
        parent::__construct();

        Config::load('./tests');
    }

    protected function createYamlProvider() {
        return new YamlProvider();
    }

    public function test_yaml_provider_parse_without_extension() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches');

        $this->assertNotEmpty($data);
    }

    public function test_yaml_provider_parse_with_extension() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches.yml');

        $this->assertNotEmpty($data);
    }

    public function test_yaml_provider_sets_id_when_parsing() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches.yml');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_yaml_provider_parses_single() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches/church-c');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('name', $entry);
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_yaml_provider_parses_recursive() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches/church-c');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('body', $entry);
            $this->assertContains('<h2>', $entry['body']);
        }
    }

    public function test_yaml_provider_exception_handling() {
        $provider = $this->createYamlProvider();
        $this->expectException(ProviderException::class);

        $provider->parse('error/churches-error');
    }
}
