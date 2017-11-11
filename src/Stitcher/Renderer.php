<?php

namespace Stitcher;

interface Renderer
{
    public function renderTemplate(string $template, array $variables): string;
}
