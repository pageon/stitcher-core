<?php

return [
    'public' => 'test',
    'nested' => [
        'item' => 'bar',
    ],
    'with' => [
        'env' => env('TEST_KEY'),
    ],
];
