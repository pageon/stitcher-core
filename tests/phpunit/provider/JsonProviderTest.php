<?php

use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\exception\ProviderException;

class JsonProviderTest extends PHPUnit_Framework_TestCase {

    protected function createJsonProvider() {
        return new JsonProvider('./setup/data');
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

        $this->assertArrayNotHasKey('churches-a', $data);
    }

    public function test_json_provider_parses_single() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches/church-a');

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('name', $data);
    }

    public function test_json_provider_parses_recursive() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches/church-b');

        $this->assertArrayHasKey('body', $data);
        $this->assertContains('<h2>', $data['body']);
    }

    public function test_json_provider_exception_handling() {
        $provider = $this->createJsonProvider();
        $this->expectException(ProviderException::class);

        $provider->parse('error/churches-error');
    }

}
