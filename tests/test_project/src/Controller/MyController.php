<?php

namespace Stitcher\Test\Controller;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MyController
{
    public function handle(Request $request, $id, $name): Response
    {
        return new Response(200, [], "test $id $name");
    }
}
