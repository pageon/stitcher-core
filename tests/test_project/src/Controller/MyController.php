<?php

namespace Stitcher\Test\Controller;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

class MyController
{
    public function handle($id, $name, Request $request): Response
    {
        return new Response(200, [], "test $id $name");
    }
}
