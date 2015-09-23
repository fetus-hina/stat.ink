var gulp = require('gulp');
var uglify = require('gulp-uglify');
var $ = require('gulp-load-plugins')();

gulp.task('gh-fork', function() {
  gulp.src('resources/gh-fork-ribbon/gh-fork-ribbon.js')
    .pipe(uglify({
      preserveComments: 'some',
      output: {
        ascii_only: true,
      },
    }))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/gh-fork-ribbon'));
});

gulp.task('main-css', function() {
  gulp.src('resources/ikaloglog/*.less')
    .pipe($.less())
    .pipe($.minifyCss({keepBreaks:true}))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/ikaloglog'))
});

gulp.task('main-js', function() {
  gulp.src('resources/ikaloglog/main.js/*.js')
    .pipe($.concat('main.js', {newLine:';'}))
    .pipe(uglify({
      preserveComments: 'some',
      output: {
        ascii_only: true,
      },
    }))
    .pipe($.gzip({gzipOptions:{level:9}}))
    .pipe(gulp.dest('resources/.compiled/ikaloglog'));
});

gulp.task('default', [
  'gh-fork',
  'main-css',
  'main-js',
]);
