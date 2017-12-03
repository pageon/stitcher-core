<?php

use Stitcher\File;

return [
    'publicDirectory' => File::path('public'),
    'sourceDirectory' => File::path('src'),
    'templateDirectory' => File::path('view'),
    'configurationFile' => File::path('src/site.yaml'),
];
