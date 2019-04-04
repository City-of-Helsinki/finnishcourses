/**
 * @file
 * Karhu Helsinki DEV gulpfile.js
 *
 * @author 8.3.2017 [Joni Kallio & Mika Lahtinen]
 */

'use strict';

// Modules
const gulp              = require('gulp'),
      sass              = require('gulp-sass'),
      sourcemaps        = require('gulp-sourcemaps'),
      imagemin          = require('gulp-imagemin'),
      newer             = require('gulp-newer'),
      fs                = require('graceful-fs'),
      path              = require('path'),
      glob              = require('glob'),
      rename            = require('gulp-rename'),
      fontmin           = require('gulp-fontmin'),
      drupalBreakpoints = require('drupal-neat-breakpoints'),
      autoprefixer      = require('gulp-autoprefixer'),
      bourbon           = require('bourbon'),
      neat              = require('bourbon-neat'),
      uglify            = require('gulp-uglify');



// Paths
const   imgSrc    = './src/images/*',
        imgDest   = './images',
        fontSrc   = './src/fonts/*',
        fontDest  = './fonts',
        sassSrcRoot = './src/sass',
        sassSrc   = sassSrcRoot + '/*.scss',
        cssTmp    = './src/tmp',
        cssDest   = './css',
        jsSrc     = './src/js/*.js',
        jsDest    = './js';


// Options
const sassOptions = {
        outputStyle: 'compressed',
        includePaths: [
          bourbon.includePaths,
          neat.includePaths
        ]
      },
    autoprefixerOptions = {
      browsers: ['last 2 versions', '> 5%']
    };



// DEV process
gulp.task('sass:dev', function () {

  return gulp
    .src(sassSrc)
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(sass(sassOptions).on('error', sass.logError))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(cssDest));

});


gulp.task('sass:watch', function () {
  gulp.watch(sassSrcRoot + '/**/!(_all).scss', [
    'sass:dev', 
    ]);
});




// Compress JS
gulp.task('compress-js', function (cb) {
  return gulp.src(jsSrc)
    .pipe(sourcemaps.init({loadMaps: true}))
    .pipe(uglify().on('error', function(e) {
      console.log('\nJavaScript uglifier:\n' + e.message + '\n'); 
      return this.end();
    }))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest(jsDest));
});

gulp.task('compress-js:watch', function() {
  gulp.watch(jsSrc, [
    'compress-js'
  ]);
});



// Image minimizer
gulp.task('imagemin', function () {
  return gulp.src(imgSrc)
      .pipe(newer(imgDest))
      .pipe(imagemin({
          progressive: true,
          svgoPlugins: [{removeViewBox: false}]
      }))
      .pipe(gulp.dest(imgDest));
});

gulp.task('imagemin:watch', function() {
  gulp.watch(imgSrc, ['imagemin']);
});


// Local font minifier
gulp.task('fontmin', function () {
  return gulp.src(fontSrc)
      .pipe(newer(fontDest))
      .pipe(fontmin())
      .pipe(gulp.dest(fontDest));
});


gulp.task('fontmin:watch', function() {
  gulp.watch(fontSrc, ['fontmin']);
});





// Get Drupal breakpoints
gulp.task('drupal-breakpoints', function () {
  return gulp.src('./finnishcourses.breakpoints.yml')
    .pipe(drupalBreakpoints.ymlToScss())
    .pipe(rename('_breakpoints.scss'))
    .pipe(gulp.dest(sassSrcRoot + '/variables'))
})

// Add the ability to import directories in Sass
// Find any _all.scss files and write @import statements
// for all other *.scss files in the same directory

// Import the whole directory with @import "somedir/all";
gulp.task('sass-includes', function (callback) {
  var all = '_all.scss';
  glob(sassSrcRoot + '/**/' + all, function (error, files) {
    files.forEach(function (allFile) {
      // Add a banner to warn users
      fs.writeFileSync(allFile, '/** This is a dynamically generated file **/\n\n');

      var directory = path.dirname(allFile);
      var partials = fs.readdirSync(directory).filter(function (file) {
        return (
          // Exclude the dynamically generated file
          file !== all &&
          // Only include _*.scss files
          path.basename(file).substring(0, 1) === '_' &&
          path.extname(file) === '.scss'
        );
      });

      // Append import statements for each partial
      partials.forEach(function (partial) {
        fs.appendFileSync(allFile, '@import "' + partial + '";\n');
      });
    });
  });

  callback();
});


gulp.task('default', ['sass:watch', 'sass-includes', 'compress-js:watch', 'compress-js', 'imagemin:watch', 'imagemin', 'fontmin:watch', 'drupal-breakpoints', 'fontmin']);
