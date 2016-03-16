require('es6-promise').polyfill();

var gulp = require('gulp');
var uglify = require('gulp-uglify');
var $ = require('gulp-load-plugins')();

function doCss(src, dest) {
  gulp.src(src)
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
    .pipe(gulp.dest(dest));
}

function doJs(src, dest) {
  gulp.src(src)
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
    .pipe(gulp.dest(dest));
}

gulp.task('gh-fork', function() {
  doJs('resources/gh-fork-ribbon/gh-fork-ribbon.js', 'resources/.compiled/gh-fork-ribbon');
});

gulp.task('gh-fork-css', function() {
  doCss('resources/gh-fork-ribbon/gh-fork-ribbon.css', 'resources/.compiled/gh-fork-ribbon');
});

gulp.task('flot-icon', function() {
  doJs('resources/flot-graph-icon/jquery.flot.icon.js', 'resources/.compiled/flot-graph-icon');
});

gulp.task('main-css', function() {
  doCss('resources/stat.ink/*.less', 'resources/.compiled/stat.ink');
});

gulp.task('main-js', function() {
  gulp.src('resources/stat.ink/main.js/*.js')
    .pipe($.concat('main.js', {newLine:';'}))
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
    .pipe(gulp.dest('resources/.compiled/stat.ink'));
});

gulp.task('activity', function() {
  doJs('resources/activity/activity.js', 'resources/.compiled/activity');
});

gulp.task('gear-calc', function() {
  doJs('resources/gears/calc.js', 'resources/.compiled/gears');
});

gulp.task('default', [
  'activity',
  'flot-icon',
  'gear-calc',
  'gh-fork',
  'gh-fork-css',
  'main-css',
  'main-js',
]);
