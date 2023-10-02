// ----------------- variable definition --------------- //
var gulp = require('gulp');
var gulpSass = require('gulp-sass');
var browserSync = require('browser-sync');
var prefixer = require('gulp-autoprefixer');
var sourcemap = require('gulp-sourcemaps');
var cleanCss = require('gulp-clean-css');
var rename = require('gulp-rename');


// ---------------------- tasks ---------------------- //

// compile sass
gulp.task('sass-compile', function () {
    return gulp.src('./sass/**/*.scss')
        .pipe(sourcemap.init())
        .pipe(gulpSass())
        .pipe(prefixer({
            overrideBrowserslist: ['last 2 versions']
        }))
        .pipe(sourcemap.write('.'))
        .pipe(gulp.dest('./css'))
    // .pipe(cleanCss({compatibility: 'ie8'}))
    // .pipe(rename({
    //     suffix: '.min'
    // }))
    // .pipe(gulp.dest('./css'));
})

// watch
gulp.task('watch', function () {
    gulp.watch('./sass/**/*.scss', gulp.series('sass-compile'));
})