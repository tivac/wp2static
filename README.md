WP2Static: A WordPress plugin to export static HTML
===================================================

We host a few sites that use WordPress as a backing CMS but wanted better performance & 
didn't want to have to have multiple WordPress installs to manage & keep up to date. This
plugin makes it so that you can export an entire WordPress site to a directory (with the 
ability to add new storage engines easily) that can then be served as a bunch of static
content by the web server of your choice.

In order to successfully use this you will need:

* A 3.x version of WordPress
* PHP 5.3+
* cURL

This plugin makes uses of the [Rolling Curl](http://code.google.com/p/rolling-curl/) PHP library.
