<?php

use Stitcher\File;

return [
    'publicDirectory' => File::path('public'),
    'sourceDirectory' => File::path('src'),
    'templateDirectory' => File::path('view'),
    'configurationFile' => __DIR__ . '/site.yaml',
];
