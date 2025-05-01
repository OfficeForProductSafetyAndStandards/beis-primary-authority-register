'use strict';

var gulp = require('gulp'),
  sass = require('gulp-sass')(require('sass')),
  sourcemaps = require('gulp-sourcemaps');

// Drupal theme directory.
var themeDir = '.';

// Gulp tasks.
gulp.task('sass', function () {
  return gulp.src(themeDir + '/sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass().on('error', sass.logError))
    .pipe(sourcemaps.write('../map/'))
    .pipe(gulp.dest(themeDir + '/css'));
});

gulp.task('assets', function () {
  return gulp.src(themeDir + '/node_modules/govuk-frontend/dist/govuk/assets/**/*')
    .pipe(gulp.dest(themeDir + '/assets'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', gulp.series('sass'));
});

gulp.task('build', gulp.series('sass', 'assets'));
gulp.task('default', gulp.series('sass', 'assets'));
