const { src, dest, parallel } = require('gulp');
var rename = require("gulp-rename");
var cleanCSS = require('gulp-clean-css');
var uglify = require('gulp-uglify');
const merge = require("merge-stream");




function css() {
    return src([
        'web/css/**/*.css',
        '!web/css/**/*.min.css'
    ])
        .pipe(cleanCSS())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(dest('web/build/css'))
}

function js() {
    return src([
        'web/js/**/*.js',
        '!web/js/**/*.min.js'
    ])
        .pipe(uglify())
        .pipe(rename({
            suffix: '.min'
        }))
        .pipe(dest('web/build/js'))
}

function vendor(){
    // Bootstrap
    var bootstrap = src([
        './node_modules/bootstrap/dist/**/*',
        '!./node_modules/bootstrap/dist/css/bootstrap-grid*',
        '!./node_modules/bootstrap/dist/css/bootstrap-reboot*'
    ])
        .pipe(dest('web/build/vendor/bootstrap'))

    // Font Awesome
    var fontAwesome = src([
        './node_modules/@fortawesome/fontawesome-free/**/*',
        '!./node_modules/@fortawesome/fontawesome-free/{less,less/*}',
        '!./node_modules/@fortawesome/fontawesome-free/{scss,scss/*}',
        '!./node_modules/@fortawesome/fontawesome-free/.*',
        '!./node_modules/@fortawesome/fontawesome-free/*.{txt,json,md}'
    ])
        .pipe(dest('web/build/vendor/font-awesome'))

    // jquery
    var jquery = src([
        './node_modules/jquery/dist/*',
        '!./node_modules/jquery/dist/core.js'
    ])
        .pipe(dest('web/build/vendor/jquery'))

    // popper.js
   var popper = src([
        './node_modules/popper.js/dist/**/*.js'
    ])
        .pipe(dest('web/build/vendor/popper.js'))

    // datatable js
   var datatableJs = src([
        './node_modules/datatables.net/js/*.js'
    ])
        .pipe(dest('web/build/vendor/datatables/js'))

// datatable css
   var datatableCss = src([
        './node_modules/datatables.net-dt/*/*',
        '!./node_modules/datatables.net-dt/*'
    ])
        .pipe(dest('web/build/vendor/datatables'))

// datatable select
   var datatableSelectJs = src([
        './node_modules/datatables.net-select/js/*.js'
    ])
        .pipe(dest('web/build/vendor/datatables/select/js'))

    var datatableSelectCss = src([
        './node_modules/datatables.net-select-dt/css/*.css'
    ])
        .pipe(dest('web/build/vendor/datatables/select/css'))

    //datatable buttons
    var datatableButtonJs = src([
        './node_modules/datatables.net-buttons/js/*.js'
    ])
        .pipe(dest('web/build/vendor/datatables/buttons/js'))

    var datatableButtonCss = src([
        './node_modules/datatables.net-buttons-dt/css/*.css'
    ])
        .pipe(dest('web/build/vendor/datatables/buttons/css'))

    var datatableBoostrap4 = src([
        './node_modules/datatables.net-bs4/css/*.css',
        './node_modules/datatables.net-bs4/js/*.js'
    ])
        .pipe(dest('web/build/vendor/datatables/styling'))

    // js-cookie
    var jsCookie = src([
        './node_modules/js-cookie/src/*.js'
    ])
        .pipe(dest('web/build/vendor/js-cookie'))
    // fastclick
    var fastclick = src([
        './node_modules/fastclick/lib/*.js'
    ])
        .pipe(dest('web/build/vendor/fastclick'))

    // jszip
    var jszip = src([
        './node_modules/jszip/dist/*.js'
    ])
        .pipe(dest('web/build/js/metrics/jszip'))

    // pdfmake
    var pdfmake = src([
        './node_modules/pdfmake/build/**/*.js'
    ])
        .pipe(dest('web/build/js/metrics/pdfmake'))

    return merge(
        bootstrap,
        fontAwesome,
        jquery,
        popper,
        datatableJs,
        datatableCss,
        datatableSelectJs,
        datatableSelectCss,
        datatableButtonJs,
        datatableButtonCss,
        datatableBoostrap4,
        jsCookie,
        fastclick,
        jszip,
        pdfmake);
}

exports.js = js;
exports.css = css;
exports.vendor = vendor;
exports.default = parallel(vendor, css, js);
