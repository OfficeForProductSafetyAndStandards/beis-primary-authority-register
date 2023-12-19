'use strict';

var gulp = require('gulp');

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', ['sass']);
});
