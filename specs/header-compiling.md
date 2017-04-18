# Header compiling

The need to be able to compile headers via Stitcher becomes clear when talking about HTTP/2 server push and
 caching headers. Ideally, every page should be able to specify its own custom headers. Because of the nature of Stitcher, 
 we're unable to add headers "on the fly", because we're only sending HTML pages.

Furthermore, a `meta` tag configured with `http-equiv` headers doesn't seem to support all headers. HTTP/2 server push 
 for example, doesn't seem to be possible using this technique (which makes a lot of sense).
  
We're left with only one option: dynamically add headers to the `.htaccess` file during compile time in production. 
 The `DevController` can set headers via PHP.
 
This approach means we need support for several things:

- Header support in pages.
- Compiling headers to the correct source (`.htaccess` or via PHP).
- In the case of production, being able to dynamically build the `.htaccess` file.
- Being able to build parts of the `.htaccess` file in case we're only compiling part of site.

## Upgrade

Header compiling support was added in Stitcher 1.0.0-alpha4. Upgrading from an older Stitcher project requires two things.

Regenerate the composer autoload mapping.   

```sh
composer dump-autoload -o
```

Add the `environment` parameter to config files.

```yaml
# config.yml
environment: production

# dev/dev.config.yml
environment: development
```

And optionally add a second parameter in the production configuration to specify where the .htaccess file is saved. This
 parameter defaults to `./public/.htaccess`.
 
```yaml
directories:
    htaccess: ./my-path/to/.htaccess
```

## Header support in pages

The `Page` class will hold a collection of HTTP headers which can be added via a public method `addHeader`. This function 
 should take the header name and header value as separate parameters.
 
Page header compiling should be triggered in the `SiteParser::parse` method, for every page.

## Compiling headers to the correct source

Because of the difference between the development and production environment in Stitcher, headers should be set different
 depending on which environment. This requires a few more classes to be added:
 
- `Brendt\Stitcher\Site\Http\HeaderCompiler`: the interface for all header compilers.
- `Brendt\Stitcher\Site\Http\HtaccessHeaderCompiler`: this class will parse headers into a `.htaccess` file.
- `Brendt\Stitcher\Site\Http\RuntimeHeaderCompiler`: this class will parse header at runtime.
- `Brendt\Stitcher\Factory\HeaderCompilerFactory`: depending on the environment, this factory gives the correct 
 `HeaderCompiler` implementation.

## `.htaccess` rendering

Stitcher will hold an object representing the `.htaccess` file in memory when compiling pages. After a page is compiled 
 to HTML, Stitcher will also add that page's id and its headers to the collection object. When all pages are compiled, 
 this object is rendered and saved to the `.htaccess` file.

- `Brendt\Stitcher\Site\Http\Htaccess`: the class which represents the `.htaccess` file and can render it.

### Partial `.htaccess` rendering

Sometimes we're only rendering one page. This should remain possible with the dynamic `.htaccess` parser, whilst not 
 clearing all other page configuration. 
