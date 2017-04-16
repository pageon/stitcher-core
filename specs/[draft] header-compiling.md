# [draft] Header compiling

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

## Header support in pages

The `Page` class will hold a collection of HTTP headers which can be added via a public method `addHeader`. This function 
 should take the header name and header value as separate parameters.
 
Page header compiling should be triggered in the `SiteParser::parse` method, for every page.
 
## Compiling headers to the correct source

## `.htaccess` rendering

Stitcher will hold an object representing the `.htaccess` file in memory when compiling pages. After a page is compiled 
 to HTML, Stitcher will also add that page's id and its headers to the collection object. When all pages are compiled, 
 this object is rendered and saved to the `.htaccess` file.

### Partial `.htaccess` rendering
