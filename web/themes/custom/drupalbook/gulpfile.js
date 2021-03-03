// Gulpfile.js running on stratumui,
// a css framework available on npmjs.com
var gulp    = require('gulp'),
  sass    = require('gulp-sass'),
  concat  = require('gulp-concat'),
  uglify  = require('gulp-uglify');

var paths = {
  styles: {
    src: 'scss/**/*.scss',
    dest: 'css'
  }
};

function styles() {
  return gulp
    .src(paths.styles.src, {
      sourcemaps: true
    })
    .pipe(sass())
    .pipe(gulp.dest(paths.styles.dest));
}

function watch() {
  gulp.watch(paths.styles.src, styles);
}

var build = gulp.parallel(styles, watch);

gulp.task(build);
gulp.task('default', build);