<?php

namespace Brendt\Stitcher\Plugin;

interface Plugin
{

    public function getConfigPath() : string;

    public function getServicesPath() : string;

}
