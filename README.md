# Stitcher

A static site generator for PHP and Smarty, with a focus on high performance.

## What is it?

A static site generator using Smarty templates, and data providers for many formats, like YAML, JSON, MarkDown, SQLite, etc. 
Also providing helper functions to optimize the critical rendering path, like responsive image support and critical CSS loading.
It's meant to be a blazing fast prototyping tool in the first place, but who knows the possibilities?

## Installation

```sh
composer require brendt/stitcher:1.0.0-alpha
php vendor/brendt/stitcher/install/stitcher site:install
```

## Usage

Stitcher will generate a static website for you, based on Smarty templates (for now) and data sources. 
As a developer, you'll mostly work in the ``src/`` directory, generating the site with ``./stitcher site:generate`` 
and debugging with an on-the-fly development controller. By running the install command, you'll create a base installation 
from which you can start. 

## Host setup

Stitcher requires at least one virtual host, two if you'd want to use the developers controller. 

**production**

```xml
<VirtualHost *:80>
    DocumentRoot "<path_to_project>/public"
    ServerName stitcher.local
    ErrorLog "<log_path>/error.log"
    CustomLog "<log_path>/access.log" common
    
    <Directory "<path_to_project>/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**development**

```xml
<VirtualHost *:80>
    DocumentRoot "<path_to_project>/dev"
    ServerName dev.stitcher.local
    ErrorLog "<log_path>/error.log"
    CustomLog "<log_path>/access.log" common
    
    <Directory "<path_to_project>/dev">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Don't forget to add a local host in ``/ets/hosts``.

```
127.0.0.1 stitcher.local
127.0.0.1 dev.stitcher.local
```

## Building a site

Click through the examples in the ``src/`` directory for a thorough understanding on how to create a site with Stitcher.

### Meta

You can configure meta tags in ``config.yml``.

```yml
meta:
    viewport: width=device-width, initial-scale=1
```

Meta tags can be automatically rendered from this config by calling the ``{meta}`` function.

```html
<head>
    {meta}
</head>
```

### CSS

There are several ways to load CSS in a Stitcher template. The ``{css}`` function is the way to go.

**Loading CSS files**

Stitcher will copy source files to the public directory and generate the correct ``<link>`` element when encoutering a ``{css}`` function.

```html
{css src='css/main.css'}
```

**Inline CSS Loading**

Tagging CSS as inline will load it directly in the DOM. Not all CSS should be loaded this way, but to make the site 
immediately look good without any extra requests, it's probably a good idea to load some CSS inline.  

```html
{css src='css/main.css' inline=true}
```

*Critical CSS loading will automatically add ``<style>`` tags.*

**SCSS and SASS**

Stitcher will automatically compile SASS and SCSS files for you, just specify the source file in the ``{css}`` function.

```html
{css src='css/main.scss' inline=true}
{css src='css/extra.scss'}
```

I'd recommend handling includes in your SASS files and not from a template file. Imports can be done from the source directory:

```css
/* ./src/css/includes.scss */
@import "css/includes";
```

### JavaScript

JS files can be loaded by calling the ``{js}`` function. This function works like the ``{css}`` function.

```html
{js src='js/main.js' inline=true}
{js src='js/extra.js'}
```

## Features

- [X] Static generator
- [X] Console support
- [X] Base installation
- [X] Performance helper functions
- [X] SASS support

#### TODO in v1

- [ ] Meta configuration
- [ ] Minifier
- [ ] Apache compression
- [ ] Refactor inconsistent directories.src usage

#### Future plans

- [ ] Base CSS
- [ ] Twig support
- [ ] Command line configuration
- [ ] Pagination and filtering
- [ ] Form support
