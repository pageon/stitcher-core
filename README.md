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

#### Host setup

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

#### Building a site

Click through the examples in the ``src/`` directory for a thorough understanding on how to create a site with Stitcher.

#### CSS

There are several ways to load CSS in a Stitcher template. The ``{css}`` function is the way to go.

**Loading CSS files**

**Critical CSS Loading**

**SCSS and SASS**

Stitcher will automatically compile SASS and SCSS files for you, just specify the source file in the ``{css}`` function.

```html
{css src='css/main.scss'}
```

I'd recommend handling includes in your SASS files and not from a template file. Imports can be done from the source directory:

```css
/* ./src/css/includes.scss */
@import "css/includes";
```

## Features

- [X] Static generator
- [X] Console support
- [X] Base installation
- [X] Performance helper functions
- [X] SASS support

#### TODO in v1

- [ ] Minifier
- [ ] Refactor inconsistent directories.src usage

#### Future plans

- [ ] Base CSS
- [ ] Twig support
- [ ] Command line configuration
- [ ] Pagination and filtering
- [ ] Form support
