# Stitcher

A static site generator for PHP and Smarty, with a focus on high performance.

```sh
# Installing Stitcher
composer require brendt/stitcher:1.0.0-alpha
php vendor/brendt/stitcher/install/stitcher site:install
```

## Building a site with Stitcher

Stitcher sites can be built by anyone with basic HTML knowledge: **data entries** are mapped onto **templates** which are accessible via **a URL**.
These two components (entries and templates) are mapped - *stitched* - together via a **config file**. Data entries can be provided in many ways:
JSON or YAML files, MarkDown files, images, SASS or CSS, JavaScript, folders and more.

The goal is simple: create blazing fast websites. Stitcher will parse all your templates into static HTML pages, will parse and minify CSS and JavaScript,
 will optimize images using ``srcset`` and provides useful developer tools to aid you in setting things up smoothly.

### site.yml

The ``site.yml`` file, located in the ``src/site`` directory is used to stitch together data and templates. The file takes a collection of URLs and some configuration parameters.

```yaml
/:
    template: home

/churches:
   template: churches/overview
   data:
       churches: churches.yml

/churches/{id}:
   template: churches/detail
   data:
       intro: intro.md
       church:
           src: churches.yml
           id: id
```

The ``template`` key is required and provides a path to the required template for this page.
 The ``data`` key isn't required. It takes a collection of variable names (these will be accessible in the template as variables).
 Each variable will need to be loaded. You can either provide a path to a data file (loaded from ``src/data`` by default),
 or you can provide a collection with a `src` and `id` key. This approach will generate detail pages from a collection of data entries.

### Data entries

Data entries can be provided in many formats: JSON, YAML, MarkDown, image, folder, ... Examples can be found after running the `site:install` command.
 A data file can either contain data of a single entry, or contain a collection of multiple entries. In the second case, when using JSON or YAML files,
 An extra root key `entries` is required.

```yaml
entries:
    church-a:
        name: Church A
        description: This is a church with the name A
        image:
            src: img/green.jpg
            alt: A green image
        body: churches/church-a.md

    church-b:
        name: Church B
        description: This is a church with the name B
        image: img/green.jpg
        body: churches/church-b.md
```

See the `src/data` folder files for a more thorough reference.

### Templates

At this moment, Stitcher only supports Smarty as a template engine. Support for more engines will be added in the future.
 In a template, all functionality of the engine is available, and all variables provided in `site.yml` are available.

```html
{extends 'index.tpl'}

{block 'content'}
    {foreach $churches as $church}
        <li>
            {$church.name}
            {if isset($church.image)}
                <img src="{$church.image.src}" srcset="{$church.image.srcset}">
            {/if}
        </li>
    {/foreach}
{/block}
```

### Helpers

Stitcher provides some helper functions in aid of creating fast websites.

```html
<html>
    <head>
        <title>Stitcher</title>

        {* The meta function will render config defined meta tags. *}
        {meta}

        {* Rendering a SCSS file, inline *}
        {css src='main.scss' inline=true}

        {* Loading a CSS file *}
        {css src='extra.css'}
    </head>
    <body>
        {block 'scripts'}
            {* Add inline JS *}
            {js src='main.js' inline=true}

            {* Load a JS file *}
            {js src='extra.js' inline=true}
        {/block}
    </body>
</html>
```

A list of all helpers:

##### {meta}
Render meta tags from `config.yml`.

##### {css src=src [inline=true]}
Load a (S)CSS file.

##### {js src=src [inline=true]}
Load a JavaSscript file.

##### {$image.src} and {$image.srcset}
Use a parsed image's `src` and `srcset` attributes.


### Config

The `config.yml` file provides some configuration options, to set directory paths, image rendering config, meta config and minification options.
 See the config file for more information.

### Commands

##### site:install

Copy a base install example.

##### site:generate [url]

Generate the whole site, or a specific URL from `sites.yml`.

##### site:clean [--force]

Remove all the generated files.

##### router:list

List all available URLs from `sites.yml`.

##### router:dispatch url

Debug a specified URL.

### Developer controller

The developer controller can be used to generate a single URL on-the-fly. Thus enabling a developer to make changes to data entries, configs or templates; and see these changes in real-time, without the need of manually generating the website again.

It's obvious that this approach takes a bit more rendering time, so web pages will be slower.

### Host setup

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

## Features

- [X] Static generator
- [X] Console support
- [X] Base installation
- [X] Performance helper functions
- [X] Meta configuration
- [X] SASS, CSS and JavaScript support
- [X] Minifier

#### TODO in v1

- [ ] Refactor inconsistent directories.src usage

#### Future plans

- [ ] Base CSS
- [ ] Twig support
- [ ] Command line configuration
- [ ] Pagination and filtering
- [ ] Form support
