<?php

use brendt\stitcher\provider\YamlProvider;

class YamlProviderTest extends PHPUnit_Framework_TestCase {

    protected function createYamlProvider() {
        return new YamlProvider('./setup/data');
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

        $this->assertArrayHasKey('id', reset($data));
    }

    public function test_json_provider_parses_single() {
        $provider = $this->createYamlProvider();

        $data = $provider->parse('churches/church-c', true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }
}
