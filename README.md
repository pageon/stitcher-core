[![Build Status](https://scrutinizer-ci.com/g/brendt/stitcher/badges/build.png?b=master)](https://scrutinizer-ci.com/g/brendt/stitcher/build-status/master) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/brendt/stitcher/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/brendt/stitcher/?branch=master)

# Stitcher

Static websites with PHP, YAML, MarkDown, JSON and Smarty/Twig.

```sh
composer require brendt/stitcher:1.0.0-alpha
php vendor/brendt/stitcher/install/stitcher site:install
```

Stitcher is a **PHP tool** for **developers** to create **high performant websites**. At its core, Stitcher is a **static site generator** capable to work with popular **template engines**, and many data formats like **MarkDown, YAML, JSON** and more. 

Besides generating static sites, Stitcher has built-in support for **minification**, **image optimisation**, and **CSS precompiling**.

It supports more advanced features than normal web pages, like **overview** and **detail** pages and **pagination**. In the near future, Stitcher will also be able to handle **filtering** and **form submissions**.

[Read the full documentation here](http://stitcher.pageon.be/)

## Features

- [X] Static site generator
- [X] Sass, CSS and JavaScript compiling and minifying
- [X] Twig and Smarty
- [X] Overviews, detail pages and pagination

#### Future plans

- [ ] URL generation
- [ ] Better config support
- [ ] Command line configuration
- [ ] Filter support
- [ ] Form support
- [ ] Better DI support for extensions