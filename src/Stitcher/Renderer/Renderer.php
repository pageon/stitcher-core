<?php

namespace Stitcher\Renderer;

interface Renderer
{
    public function renderTemplate(string $template, array $variables): string;

    public function customExtension(Extension $function): void;
}
