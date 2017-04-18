# [draft] HTTP/2 server push 

This document describes how Stitcher implements HTTP/2 (further referenced as h2) server push support. Enabling h2 server
 push in Stitcher will not break on any servers that don't support any of the following requirements. This feature is
 completely backwards compatible.

### Prerequisites

Stitcher will only support Apache at this point in time. This Apache server must be correctly configured to support h2 server push. 

This includes: 
- SSL enabled and configured virtual host.
- Apache 2.4.17 or higher.
- `mod_http2` and `mod_headers` enabled.
- Enabled `.htaccess` support (which is a requirement for Stitcher anyway).

## Header support

h2 server push headers will reside in the `mod_headers` conditional block:

```apacheconfig
<ifmodule mod_headers.c>
    # ...
</ifmodule>
```

Every page will have a separate `FilesMatch` rule in which custom headers for that page can be added.

```apacheconfig
<FilesMatch "^\/page-id$">
    Header add Link "</main.css>; rel=preload; as=style"
    Header add Link "</img/image.jpg>; rel=preload; as=image"
    Header add Link "</js/script.js>; rel=preload; as=script"
</FilesMatch>
```

## `Page` integration
 
Header values should be set during compile time in `Parser` classes. Furthermore, the `TemplatePlugin` class should also
 support the server push functionality. When a template function requests a resource to be pushed instead of loaded inline
 or external, that function should also add the needed headers for that resource.

## Template functions

A few template function support the new `push` argument. Setting this argument to `true` will make that resource available
 via h2 server push. These function are the following (Smarty syntax).
 
`{css src='/css/main.css' push=true}`

`{js src='/js/main.js' push=true}`

`{image src='/img/image.jpg' push=true}`

`{file src='/path/to/file.pdf' push=true}`

## Parser support

At this point in time, the only parser for which it makes sense to support h2 server push is the `ImageParser`. However, 
 this goes against the responsive image support in Stitcher, because the browser has to decide which image it should request.
 For now it seems unlikely h2 server push should be enabled for parsers.
 
This issue might be solved in the future with client hint support, enabling the server the know the dimensions of the 
 client's window.
 
## Configuration

One option might be useful to add, enabling server push by default. All resources will be pushed, and only specific resources 
 will be loaded on the client's request.  
