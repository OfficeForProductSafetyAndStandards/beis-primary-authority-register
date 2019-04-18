'use strict';

var gulp = require('gulp'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps');

// Drupal theme directory.
var themeDir = './web/themes/custom/par_theme';
var librariesDir = './web/libraries';

// Gulp tasks.
gulp.task('sass', function () {
  return gulp.src(themeDir + '/sass/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules/govuk_frontend_toolkit/stylesheets', // 1
        'node_modules/govuk-elements-sass/public/sass'     // 2
      ]
    }).on('error', sass.logError))
    .pipe(sourcemaps.write('../map/'))
    .pipe(gulp.dest(themeDir + '/css'));
});

gulp.task('cp-assets', function() {
  return gulp.src([
    "node_modules/govuk_frontend_toolkit/images/**"
  ])
  .pipe(gulp.dest(themeDir + '/assets/vendor/images'));
});
gulp.task('chosen-library', function() {
  return gulp.src([
    "node_modules/chosen-js/**"
  ])
  .pipe(gulp.dest(librariesDir + '/chosen'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', ['sass']);
});

gulp.task('build', gulp.series('chosen-library', 'cp-assets', 'sass'));
gulp.task('default', gulp.series('chosen-library', 'cp-assets', 'sass'));
