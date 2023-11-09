'use strict';

var gulp = require('gulp'),
  sassVariables = require('gulp-sass-variables'),
  sass = require('gulp-sass'),
  sourcemaps = require('gulp-sourcemaps');

let argv = require('yargs').options({
  'govuk_compatibility_govukelements': {
    alias: 'ce',
    describe: 'If you’re using or migrating from GOV.UK Elements build with this option',
    type: 'boolean'
  },
  'govuk_compatibility_govuktemplate': {
    alias: 'ct',
    describe: 'If you’re using or migrating from GOV.UK Template build with this option',
    type: 'boolean'
  },
  'govuk_compatibility_govukfrontend': {
    alias: 'cf',
    describe: 'If you’re using or migrating from the old GOV.UK Frontend Toolkit build with this option',
    type: 'boolean'
  },
  'govuk_use_legacy_palette': {
    alias: 'cl',
    describe: 'If you’re not using any of our old frameworks, you can still configure GOV.UK Frontend to use the old colour palette.',
    type: 'boolean'
  }
}).argv;

// Drupal theme directory.
var themeDir = '.';

// Gulp tasks.
gulp.task('sass', function () {
  return gulp.src(themeDir + '/sass/**/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sassVariables({
      '$govuk-compatibility-govukelements': !!argv.ce,
      '$govuk-compatibility-govuktemplate': !!argv.ct,
      '$govuk-compatibility-govukfrontendtoolkit': !!argv.cf,
      '$govuk-use-legacy-palette': !!argv.cl
    }))
    .pipe(sass({
      includePaths: [
        'node_modules'     // 2
      ]
    }).on('error', sass.logError))
    .pipe(sourcemaps.write('../map/'))
    .pipe(gulp.dest(themeDir + '/css'));
});

gulp.task('assets', function () {
  return gulp.src(themeDir + '/node_modules/govuk-frontend/govuk/assets/**/*')
    .pipe(gulp.dest(themeDir + '/assets'));
});

gulp.task('watch', function() {
  gulp.watch(themeDir + '/sass/**/*.scss', gulp.series('sass'));
});


gulp.task('build', gulp.series('sass', 'assets'));
gulp.task('default', gulp.series('sass', 'assets'));
