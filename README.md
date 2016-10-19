# Stitcher

A static site generator for PHP and Smarty, with a focus on high performance.

## What is it?

A static site generator using Smarty templates, and data providers for many formats, like YAML, JSON, MarkDown, SQLite, etc. 
Also providing helper functions to optimize the critical rendering path. Supporting responsive images, art direction, async asset loading, client type hinting, critical CSS loading, etc.
It's meant to be a prototyping tool in the first place, but who knows the possibilities?

## Installation

```sh
composer require brendt/stitcher:1.0.0-alpha
php vendor/brendt/stitcher/install/stitcher site:install
```

## Usage

## Roadmap

- [X] Static generator
- [X] Console support
- [X] Base installation
- [X] Performance helper functions

#### TODO in v1

- Refactor inconsistent directories.src usage

#### Future plans

- Add Twig support
- Base CSS
- Pagination and filtering
- Sass compiler
- Minifier
- Command line configuration
- Form support
