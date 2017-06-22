'use strict';

var gulp = require('gulp'),
	sass = require('gulp-sass'),
	sourcemaps = require('gulp-sourcemaps');

// Drupal theme directory.
var themeDir = 'web/themes/custom/par_theme/assets/stylesheets/';

// Gulp tasks.
gulp.task('sass', function () {
 return gulp.src(themeDir + '*.scss')
  .pipe(sourcemaps.init())
  .pipe(sass().on('error', sass.logError))
  .pipe(sourcemaps.write(themeDir))
  .pipe(gulp.dest(themeDir));
});

gulp.task('watch', function() {
  gulp.watch(themeDir, ['sass']);
});

gulp.task('build', ['sass']);
gulp.task('default', ['sass']);
