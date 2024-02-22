const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/src/app.js', 'public/js')
    .webpackConfig({
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js/src'),
                // '@assets': path.resolve(__dirname, 'resources/assets'),
                '@sass': path.resolve(__dirname, 'resources/sass')
            }
        }
    })
   .sass('resources/sass/app.scss', 'public/css',{
    implementation: require('node-sass')
   })