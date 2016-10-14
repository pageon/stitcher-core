<?php

use brendt\stitcher\provider\JsonProvider;
use brendt\stitcher\exception\ProviderException;
use brendt\stitcher\Config;

class JsonProviderTest extends PHPUnit_Framework_TestCase {

    /**
     * JsonProviderTest constructor.
     */
    public function __construct() {
        parent::__construct();

        Config::load('./tests');

        // TODO: Refactor providerFactory with DI
        $stitcher = new \brendt\stitcher\Stitcher();
    }

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

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
        }
    }

    public function test_json_provider_doenst_parse_subfolders() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches');

        $this->assertArrayNotHasKey('churches-a', $data);
    }

    public function test_json_provider_parses_single() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches/church-a');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('id', $entry);
            $this->assertArrayHasKey('name', $entry);
        }
    }

    public function test_json_provider_parses_recursive() {
        $provider = $this->createJsonProvider();

        $data = $provider->parse('churches/church-b');

        foreach ($data as $entry) {
            $this->assertArrayHasKey('body', $entry);
            $this->assertContains('<h2>', $entry['body']);
        }
    }

    public function test_json_provider_exception_handling() {
        $provider = $this->createJsonProvider();
        $this->expectException(ProviderException::class);

        $provider->parse('error/churches-error');
    }

}
