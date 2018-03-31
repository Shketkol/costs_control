var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    .addEntry('app', './assets/assets/app.js')
    .addEntry('auth', './assets/assets/auth.js')
    .addEntry('selectpicker', './assets/assets/selectpicker.js')
    .addEntry('sweetalerts', './assets/assets/sweetalerts.js')
    // .addStyleEntry('css/app', './assets/css/common.scss')

    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // Load less files
    .enableLessLoader()

    // allow legacy applications to use $/jQuery as a global variable
    .autoProvidejQuery()

    .enableSourceMaps(!Encore.isProduction())

    // empty the outputPath dir before each build
    .cleanupOutputBeforeBuild()

    // show OS notifications when builds finish/fail
    .enableBuildNotifications()

    .configureFilenames({
        images: '[path][name].[ext]'
    })
;

module.exports = Encore.getWebpackConfig();
