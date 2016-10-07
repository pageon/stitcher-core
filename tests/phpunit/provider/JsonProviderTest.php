<?php

use brendt\stitcher\dataProvider\JsonProvider;

class JsonProviderTest extends PHPUnit_Framework_TestCase {

    protected function createJsonProvider() {
        return new JsonProvider('./tests/src');
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

}
