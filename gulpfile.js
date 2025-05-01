'use strict';

import gulp from 'gulp';
import gulpsass from 'gulp-sass';

const gulp = require('gulp')
const sass = require('gulp-sass')(require('sass'));

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', ['sass']);
});
