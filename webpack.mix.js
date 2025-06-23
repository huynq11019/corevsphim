const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
   .js('resources/js/jquery.min.js', 'public/js')
   .js('resources/js/jquery.slimscroll.min.js', 'public/js')
   .js('resources/js/bootstrap2.min.js', 'public/js')
   .js('resources/js/jquery.lazyload.min.js', 'public/js')
   .js('resources/js/jquery.core.min.js', 'public/js')
    .js('resources/js/jquery.raty.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        //
    ]);
