<?php

namespace Stitcher;

use Stitcher\Renderer\Extension;

interface Renderer
{
    public function renderTemplate(string $template, array $variables): string;

    public function customExtension(Extension $function): void;
}
