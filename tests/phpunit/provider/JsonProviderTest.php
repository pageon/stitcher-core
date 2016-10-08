<?php

use brendt\stitcher\provider\JsonProvider;

class JsonProviderTest extends PHPUnit_Framework_TestCase {

    protected function createJsonProvider() {
        return new JsonProvider('./tests/src/data');
    }

    public function test_json_provider_parse_without_extension() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches');

        $this->assertNotEmpty($data);
    }

    public function test_json_provider_parse_with_extension() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches.json');

        $this->assertNotEmpty($data);
    }

    public function test_json_provider_sets_id_when_parsing() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches');

        $this->assertArrayHasKey('id', reset($data));
    }

    public function test_json_provider_doenst_parse_subfolders() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches');

        $this->assertArrayNotHasKey('churches-c', $data);
    }

    public function test_json_provider_parses_single() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches/church-c', true);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

}
