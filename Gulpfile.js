// fix problems with undefined Promise class
// http://stackoverflow.com/questions/32490328/gulp-autoprefixer-throwing-referenceerror-promise-is-not-defined
require('es6-promise').polyfill();

// Load plugins
var gulp = require('gulp'),
    sass = require('gulp-sass'),
    autoprefixer = require('gulp-autoprefixer'),
    cssnano = require('gulp-cssnano'),
    jshint = require('gulp-jshint'),
    uglify = require('gulp-uglify'),
    imagemin = require('gulp-imagemin'),
    rename = require('gulp-rename'),
    concat = require('gulp-concat'),
    notify = require('gulp-notify'),
    cache = require('gulp-cache'),
    browsersync = require('browser-sync'),
    sourcemaps = require('gulp-sourcemaps'),
    spritesmith = require('gulp.spritesmith'),
    del = require('del'),
    gulpif = require('gulp-if'),
    runSequence = require('run-sequence');

var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'expanded'
};

var autoprefixerOptions = {
  browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};


// Styles
gulp.task('styles', function() {
  return gulp.src('scss/all.scss')
    .pipe(sourcemaps.init())
    .pipe(sass(sassOptions).on('error', sass.logError))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(sourcemaps.write('.', { sourceRoot: '../../scss/' }))
    .pipe(gulp.dest('web/css'))
    .pipe(gulpif('*.css', rename({ suffix: '.min' })))
    .pipe(gulpif('*.css', cssnano()))
    .pipe(gulpif('*.css', gulp.dest('web/css')))
    .pipe(gulpif('*.css', notify({ message: 'Styles task complete' })));
});

// sprites
gulp.task('sprites', function () {
    var spriteData = gulp.src('data/avatars/*')
        .pipe(spritesmith({
            imgName: '../image/sprite.png',
            cssName: 'contributors.css'
        }));
    spriteData.img.pipe(gulp.dest('web/image'));
    spriteData.css.pipe(gulp.dest('scss/2-vendors'));
});

// Scripts
gulp.task('scripts', function() {
  return gulp.src(require('./js/all.json'))
    .pipe(sourcemaps.init())
    .pipe(concat('all.js'))
    .pipe(sourcemaps.write('.', { sourceRoot: '../../js/' }))
    .pipe(gulp.dest('web/js'))
    .pipe(gulpif('*.js', rename({ suffix: '.min' })))
    .pipe(gulpif('*.js', uglify()))
    .pipe(gulpif('*.js', gulp.dest('web/js')))
    .pipe(gulpif('*.js', notify({ message: 'Scripts task complete' })));
});

// Copy fonts
gulp.task('fonts', function() {
  return gulp.src(
    [   'vendor/bower/bootstrap-sass/assets/fonts/bootstrap/*',
        'vendor/bower/font-awesome/fonts/*',
        'scss/2-vendors/ptsans-bold/fonts/*',
        'scss/2-vendors/ptsans-regular/fonts/*',
        'scss/2-vendors/sourcecodepro/fonts/*'
    ])
    .pipe(gulp.dest('./web/fonts'));
});

// Clean
gulp.task('clean', function() {
  return del(['web/css/*', 'web/js/*', 'web/fonts/*']);
});

// Build the "web" folder by running all of the above tasks
gulp.task('build', function(callback) {
  runSequence('clean', ['styles', 'scripts', 'fonts'], callback);
});

// Watch
gulp.task('watch', function() {

  // Initialize Browsersync
  browsersync.init({
    proxy: "http://local.yiiframework.com"
  });

  // Watch .scss files
  gulp.watch('scss/**/*.scss', ['styles']);

  // Watch .js files
  gulp.watch('js/**/*.js', ['scripts']);

  // Watch any view files in 'views', reload on change
  gulp.watch(['views/**/*.php']).on('change', browsersync.reload);

  // Watch any files in 'web', reload on change
  gulp.watch(['web/js/*']).on('change', browsersync.reload);
  gulp.watch(['web/css/*']).on('change', browsersync.reload);
});

gulp.task('default', ['build', 'watch'], function() {});
