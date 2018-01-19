<?php

\Stitcher\App::router()
    ->get('/test/{id}/{name}', \Stitcher\Test\Controller\MyController::class)
;
