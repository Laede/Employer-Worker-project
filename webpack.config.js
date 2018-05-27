var Encore = require('@symfony/webpack-encore');

Encore
// the project directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')

    // Delete compiled files on rebuild
    .cleanupOutputBeforeBuild()

    // Add source maps (the ability to trace back minified file to source) to minified files
    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.css) to break cache
    .enableVersioning(Encore.isProduction())

    .createSharedEntry('vendor', [
        'jquery',
        'bootstrap/dist/css/bootstrap.css',
        'bootstrap/dist/js/bootstrap.js',
        'bootstrap-select-v4/dist/css/bootstrap-select.css',
        'bootstrap-select-v4/dist/js/bootstrap-select.js'
    ])

    // JS of the project
    .addEntry('js/app', './assets/js/app.js')


    // Css
    .addStyleEntry('css/app', './assets/css/app.scss')


    // Using SASS
    .enableSassLoader()

    // Using JS - needed for bootstrap
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();