var gulp = require('gulp');
var browsersync = require('browser-sync');

var sourcemaps = require('gulp-sourcemaps');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');
var rename = require('gulp-rename');
var cssnano = require('gulp-cssnano');
var uglify = require('gulp-uglify');
var gulpif = require('gulp-if');
var concat = require('gulp-concat');
var util = require('gulp-util');

var del = require('del');
var spritesmith = require('gulp.spritesmith');
var config = require('./config');
var isProduction = util.env.production;

var sassOptions = {
  errLogToConsole: true,
  outputStyle: 'expanded',
  includePaths: config.PATHS.sass
};

var autoprefixerOptions = {
  browsers: config.COMPATIBILITY
};

gulp.task('styles', function () {
  return gulp.src(config.PATHS.src + '/scss/all.scss')
    .pipe(gulpif(!isProduction, sourcemaps.init()))
    .pipe(sass(sassOptions))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(gulpif(isProduction, rename({suffix: '.min'})))
    .pipe(gulpif(isProduction, cssnano()))
    .pipe(gulpif(!isProduction, sourcemaps.write('.', {sourceRoot: '../../assets/src/scss/'})))
    .pipe(gulp.dest(config.PATHS.dist + '/css'))
    .pipe(gulpif(!isProduction, browsersync.stream()));
});

gulp.task('forumheader', function () {
  return gulp.src(config.PATHS.src + '/scss/header.scss')
    .pipe(sourcemaps.init())
    .pipe(sass(sassOptions))
    .pipe(autoprefixer(autoprefixerOptions))
    .pipe(gulp.dest(config.PATHS.dist + '/css'));
});

gulp.task('scripts', function () {
  return gulp.src(config.PATHS.javascript)
    .pipe(sourcemaps.init())
    .pipe(concat('all.js'))
    .pipe(gulpif(isProduction, rename({suffix: '.min'})))
    .pipe(gulpif(isProduction, uglify()))
    .pipe(gulpif(!isProduction, sourcemaps.write('.', {sourceRoot: '../../assets/src/js/'})))
    .pipe(gulp.dest(config.PATHS.dist + '/js'));
});

gulp.task('sprites', function () {
  var spriteData = gulp.src('data/avatars/*')
    .pipe(spritesmith({
      imgName: 'sprite.png',
      imgPath: '../../../image/sprite.png',
      cssName: 'contributors.css',
      padding: 2
    }));
  spriteData.img.pipe(gulp.dest('web/image'));
  spriteData.css.pipe(gulp.dest(config.PATHS.src + '/scss/2-vendors'));
  return spriteData;
});

gulp.task('fonts', function () {
  return gulp.src(config.PATHS.fonts)
    .pipe(gulp.dest(config.PATHS.dist + '/fonts'));
});

gulp.task('clean', function () {
   return del(config.PATHS.dist)
});

gulp.task('watch', function () {
  browsersync.init({
    proxy: config.PROXY
  });

  gulp.watch(config.PATHS.src + '/scss/**/*.scss', gulp.series('styles'));
  gulp.watch(config.PATHS.src + '/scss/**/*.scss', gulp.series('forumheader'));
  gulp.watch(config.PATHS.src + '/js/**/*.js', gulp.series('scripts'));
  gulp.watch(['views/**/*.php']).on('change', browsersync.reload);
  gulp.watch([config.PATHS.dist + '/js/*']).on('change', browsersync.reload);
  gulp.watch([config.PATHS.dist + '/css/*']).on('change', browsersync.reload);
});

gulp.task('build', gulp.series('clean', 'sprites', gulp.parallel('styles', 'forumheader', 'scripts', 'fonts')));

gulp.task('default', gulp.series('build', 'watch'));
