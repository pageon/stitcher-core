# Stitcher

A static site generator for PHP and Smarty, with a focus on high performance.

## Overview loader

route:
    template:
    data:
        entries: source.yml
        intro: source.md
        
source.yml > parse > [entries: [idA, idB]]
intro.md > parse > $md
- $html = template with $entries = $entries, $intro = $md
- $blanket[$route] = $html 


## Detail loader

route/{id}:
    template:
    data:
        entry: source.yml
        intro: source.md
        
source.yml > parse > [entries: [idA, idB]]
intro.md > parse > $md
foreach $entries as $entry
    - create $routeName route/idA
    - $html = template with $entry = $entries[$idA], $intro = $md
    - $blanket[$routeName] = $html 

## TODO

- Dev controller
- DI support for factories
- Smarty provider support

## What is it?

A static site generator using Smarty templates, and data providers for many formats, like YAML, JSON, MarkDown, SQLite, etc. 
Also providing helper functions to optimize the critical rendering path. Supporting responsive images, art direction, async asset loading, client type hinting, critical CSS loading, etc.
It's meant to be a prototyping tool in the first place, but who knows the possibilities?

## Roadmap

- [ ] Static generator
- [ ] Critical functions
- [ ] Console support
- [ ] Base installation

## Future plans

- Form support
