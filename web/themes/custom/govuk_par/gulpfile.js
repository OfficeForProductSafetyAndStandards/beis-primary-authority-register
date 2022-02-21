'use strict';

let gulp = require('gulp'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps');

// Drupal theme directory.
const themeDir = '.';

// Gulp tasks.
gulp.task('sass', function () {
  return gulp.src('./sass/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({
      includePaths: [
        'node_modules'
      ]
    }).on('error', sass.logError))
    .pipe(sourcemaps.write('../map/'))
    .pipe(gulp.dest(themeDir + '/css'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', gulp.series('sass'));
});

gulp.task('build', gulp.series('sass'));
gulp.task('default', gulp.series('sass'));
