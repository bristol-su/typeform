const mix = require('laravel-mix');

mix.setPublicPath('./public');

mix.js('resources/js/module.js', 'public/modules/typeform/js')
    .sass('resources/sass/module.scss', 'public/modules/typeform/css');

mix.webpackConfig({
    externals: {
        '@bristol-su/frontend-toolkit': 'Toolkit',
        'vue': 'Vue',
    }
});
