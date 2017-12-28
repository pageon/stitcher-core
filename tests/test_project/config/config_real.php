<?php

use Stitcher\File;

return [
    'plugins' => [
        \Stitcher\Test\Plugin\TestPlugin::class,
    ],

    'publicDirectory' => File::path('public'),
    'sourceDirectory' => File::path('src'),
    'templateDirectory' => File::path('resources/view'),
    'configurationFile' => File::path('src/site.yaml'),
];
