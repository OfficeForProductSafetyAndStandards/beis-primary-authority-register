'use strict';

var gulp = require('gulp');

// Drupal libraries directory.
var librariesDir = './web/libraries';

// Gulp tasks.
gulp.task('chosen-library', function() {
  return gulp.src([
    "node_modules/chosen-js/**"
  ])
  .pipe(gulp.dest(librariesDir + '/chosen'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', ['sass']);
});

gulp.task('build', gulp.series('chosen-library'));
gulp.task('default', gulp.series('chosen-library'));
