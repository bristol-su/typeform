const mix = require('laravel-mix');

mix.setPublicPath('./public');

mix.js('resources/js/module.js', 'public/modules/typeform/js')
    .js('resources/js/components.js', 'public/modules/typeform/js')
    .sass('resources/sass/module.scss', 'public/modules/typeform/css');
