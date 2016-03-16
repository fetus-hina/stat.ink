require('es6-promise').polyfill();

var path = require('path');
var gulp = require('gulp');
var uglify = require('gulp-uglify');
var $ = require('gulp-load-plugins')();
var argv = require('minimist')(process.argv.slice(2));

gulp.task('css', function () {
  gulp.src(argv.in)
    .pipe($.postcss([
      require('autoprefixer')({
        browers: [
          'IE >= 9',
          'iOS >= 7',
          'Android >= 2.3',
          'Firefox ESR',
          'last 2 versions',
          '> 5%',
        ],
      }),
      require('css-mqpacker')(),
      require('csswring')(),
    ]))
    .pipe($.rename(path.basename(argv.out)))
    .pipe(gulp.dest(path.dirname(argv.out)));
});

gulp.task('less', function () {
  gulp.src(argv.in)
    .pipe($.less())
    .pipe($.postcss([
      require('autoprefixer')({
        browers: [
          'IE >= 9',
          'iOS >= 7',
          'Android >= 2.3',
          'Firefox ESR',
          'last 2 versions',
          '> 5%',
        ],
      }),
      require('css-mqpacker')(),
      require('csswring')(),
    ]))
    .pipe($.rename(path.basename(argv.out)))
    .pipe(gulp.dest(path.dirname(argv.out)));
});

gulp.task('js', function () {
  gulp.src(argv.in)
    .pipe($.concat('tmp.js', {newLine:';'}))
    .pipe(
      uglify({
        output: {
          ascii_only: true,
          comments: function (node, comment) {
            return comment.type === 'comment2' && (comment.value + "").substr(0, 1) === '!';
          },
        },
        compress: {
          unsafe: true,
        },
      })
    )
    .pipe($.rename(path.basename(argv.out)))
    .pipe(gulp.dest(path.dirname(argv.out)));
});
