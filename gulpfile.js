'use strict';

var gulp = require('gulp'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps');

// Drupal theme directory.
var themeDir = './web/themes/custom/par_theme';

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
    .pipe(sourcemaps.write('../maps/'))
    .pipe(gulp.dest(themeDir + '/css'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', ['sass']);
});

gulp.task('build', ['sass']);
gulp.task('default', ['sass']);
