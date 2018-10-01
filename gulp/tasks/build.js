///////////
// Build //
///////////

import gulp from 'gulp';
import preprocess from 'gulp-preprocess';
import config from '../config';
import phpunit from 'gulp-phpunit';
import jest from 'gulp-jest';
import replace from 'gulp-replace';
import zip from 'gulp-zip';
import flatten from 'gulp-flatten';
import rsync from 'gulp-rsync';
import childProcess from 'child_process';
import del from 'del';
import git from 'gulp-git';
import shell from 'gulp-shell';


/*

Copies all files and folders assigned to `config.files.all` to the _tmp dir

This does _not_ carry over the node_modules folder. The reason is becauase node
will resolve the nessesary dependencies by looking to the parent folder (which in
our case is the main plugin folder that _does_ have node_modules.

More info on node dependency resolution: https://nodejs.org/api/modules.html#modules_loading_from_node_modules_folders

*/
gulp.task('build:copy', () => {

  return gulp
    .src( config.files.all )
    .pipe( gulp.dest(config.folders.tmp) );

});


gulp.task('build:free:copy', () => {

  return gulp
    .src( config.files.onlyWorking )
    .pipe( gulp.dest(config.folders.tmp) );

});


gulp.task('build:free:repo', () => {
  return git.clone('git@github.com:wpshopify/wp-shopify.git', { args: './_free' });
});


gulp.task('build:free:repo:copy', () => {

  return gulp
    .src(config.files.tmpAll)
    .pipe(gulp.dest(config.folders.freeRepo));

});


/*

Runs preprocess
- gulp.src always refers to files within _tmp folder

*/
gulp.task('build:preprocess', () => {

  return gulp
    .src( config.files.toBeProcessed, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace('<?php ?>', function(match, p1, offset, string) {
      console.log('\x1b[33m%s\x1b[0m', 'Notice: replaced ' + match + ' in file: ' + this.file.relative);
      return '';

    }))
    .pipe( gulp.dest("./") );

});


/*

Changes Plugin name
- gulp.src always refers to files within _tmp folder

*/
gulp.task('build:rename:plugin', done => {

  return gulp
    .src( config.files.entry, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace('WP Shopify Pro', function(match, p1, offset, string) {

      if (config.buildTier === 'free') {
        console.log('\x1b[33m%s\x1b[0m', 'Notice: replaced ' + match + ' with WP Shopify in file: ' + this.file.relative);
        return 'WP Shopify';

      } else {
        return match;
      }

    }))
    .pipe( gulp.dest("./") );

});


/*

Changes Plugin version
- gulp.src always refers to files within _tmp folder

*/
gulp.task('build:rename:version', done => {

  return gulp
    .src( config.files.versionLocations, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace(config.currentRelease, function(match, p1, offset, string) {

      console.log('\x1b[33m%s\x1b[0m', 'Notice: replaced ' + match + ' with ' + config.buildRelease + ' in file: ' + this.file.relative);
      return config.buildRelease;

    }))
    .pipe( gulp.dest("./") );

});


/*

Changes the main plugin Class. Allows both plugins to be loaded simultanously

*/
gulp.task('build:rename:class', done => {

  return gulp
    .src( config.files.entry, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace('WP_Shopify_Pro', function(match, p1, offset, string) {

      if (config.buildTier === 'free') {
        console.log('\x1b[33m%s\x1b[0m', 'Notice: replaced ' + match + ' with WP_Shopify in file: ' + this.file.relative);
        return 'WP_Shopify';

      } else {
        return match;
      }

    }))
    .pipe( gulp.dest("./") );

});


/*

Changes the main plugin Class. Allows both plugins to be loaded simultanously

*/
gulp.task('build:rename:misc', done => {

  return gulp
    .src( config.files.pluginTitleSettings, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace("'WP Shopify Pro', WPS_PLUGIN_TEXT_DOMAIN", function(match, p1, offset, string) {

      if (config.buildTier === 'free') {
        console.log('\x1b[33m%s\x1b[0m', 'Notice: replaced ' + match + ' with WP Shopify in file: ' + this.file.relative);
        return "'WP Shopify', WPS_PLUGIN_TEXT_DOMAIN";

      } else {
        return match;
      }

    }))
    .pipe( gulp.dest("./") );

});


/*

Ensures we comment out the test version number

*/
gulp.task('build:remove:testversion', done => {

  return gulp
    .src( config.files.pluginUpdateFunction, { base: "./" } )
    .pipe(preprocess({
      context: {
        NODE_ENV: config.buildTier
      }
    }))
    .pipe(replace("$new_version_number = '", function(match, p1, offset, string) {

      console.log('\x1b[33m%s\x1b[0m', 'Notice: Commented out ' + match + ' in file: ' + this.file.relative);
      return "// $new_version_number = '";

    }))
    .pipe( gulp.dest("./") );

});


/*

Runs tests for php via PHPUnit

*/
gulp.task('test:php', () => {

  return gulp
    .src(config.folders.plugin)
    .pipe(phpunit('/usr/local/bin/phpunit', {
      stopOnFailure: true
    }));

});


/*

Runs tests for js via Jest

*/
gulp.task('test:js', () => {

  return gulp
    .src(config.folders.plugin)
    .pipe( shell([
      'npm run tests-client-unit-no-watch',
      'npm run tests-client-integration'
    ]));

});


/*

Runs all tests in parallel

*/
gulp.task('tests', done => {
  return gulp.parallel('test:php', 'test:js')(done);
});


/*

Zip up files in _tmp folder
Requires:
--tier=""
--release=""

*/
gulp.task('build:zip', done => {

  var zipName = config.buildTier === 'pro' ? 'wp-shopify-pro.zip' : 'wpshopify.zip';

  return gulp
    .src( config.files.tmp, { base: "./" } )
    .pipe( zip(zipName) )
    .pipe( gulp.dest(config.folders[config.buildTier]) );

});


/*

Removes various files / folders from free verson. At this point preprocessing has finished.
We're simply cleaning up files that the free version won't ever use.

*/
gulp.task('build:clear:free', done => {

  if (config.buildTier !== 'free') {
    return done();
  }

  return del(config.files.buildFreeClear, { force: true });

});


/*

Removes superfluous files / folders from dist copy

Currently only removes admin and public JavaScript app code -- leaves vendor files

*/
gulp.task('build:clear:superfluous', done => {

  return del(config.files.buildSuperfluousClear, { force: true });

});


/*

Zip up files in _tmp folder

Requires:
--tier=""
--release=""

*/
gulp.task('build:zip:deploy', done => {

  if (config.buildTier === 'pro') {
    var command = 'rsync -avz /Users/arobbins/www/wpstest/assets/wp-shopify-pro/wp-shopify-pro.zip arobbins@162.243.170.76:~';

  } else {
    var command = 'rsync -avz /Users/arobbins/www/wpstest/assets/wpshopify/wpshopify.zip arobbins@162.243.170.76:~';
  }

  return childProcess.exec(command, function (err, stdout, stderr) {

    if (err !== null) {
      console.log('Error build:zip:deploy: ', err);
      return;
    }

  });

});


/*

Zip up files in _tmp folder

Requires:
--tier=""
--release=""

*/
gulp.task('build:zip:move', done => {

  var zipName = 'wpshopify.zip';
  var tier = 'free';

  if (config.buildTier === 'pro') {
    tier = 'pro';
    zipName = 'wp-shopify-pro.zip';
  }

  var command = 'ssh -tt arobbins@162.243.170.76 "rm -rf /var/www/prod/html/' + tier + '/releases/' + config.buildRelease + ' && mkdir  -p /var/www/prod/html/' + tier + '/releases/' + config.buildRelease + ' && mv ' + zipName + ' /var/www/prod/html/' + tier + '/releases/' + config.buildRelease + ' && chmod 755 /var/www/prod/html/' + tier + '/releases/' + config.buildRelease + '/' + zipName + '"';

  return childProcess.exec(command, function (err, stdout, stderr) {

    if (err !== null) {
      console.log('Error build:zip:move: ', err);
      return;
    }

  });

});


/*

Requires:
--tier=""
--release=""

*/
gulp.task('build:dist', done => {

  return gulp.series(
    'build:clear:free',
    'build:clear:superfluous',
    'build:zip',
    'build:zip:deploy',
    'build:zip:move'
  )(done);

});


/*

Zip up files in _tmp folder

Requires:
--tier=""
--release=""

*/
gulp.task('build:update:edd', done => {

  var tier = 'free';

  if (config.buildTier === 'pro') {
    tier = 'pro';
  }

  //
  // Prod build
  //
  var command = 'ssh -tt arobbins@162.243.170.76 "php -f /var/www/staging/html/wp-content/themes/wpshop/lib/updates/update-product-info.php ' + config.buildRelease + ' ' + tier +' && php -f /var/www/prod/html/wp-content/themes/wpshop/lib/updates/update-product-info.php ' + config.buildRelease + ' ' + tier + '"';

  //
  // Staging build
  //
  // var command = 'ssh -tt arobbins@162.243.170.76 "php -f /var/www/staging/html/wp-content/themes/wpshop/lib/updates/update-product-info.php ' + config.buildRelease + ' ' + tier + '"';

  return childProcess.exec(command, function (err, stdout, stderr) {

    if (err !== null) {
      console.log('Error build:zip:move: ', err);
      return;
    }

  });


});


/*

Runs all build tasks

Requires:
--tier=""
--release=""

*/
gulp.task('build', done => {

  return gulp.series(
    'tests',
    'clean:tmp',
    'clean:free:repo',
    'build:copy',
    'build:preprocess',
    'build:rename:plugin',
    'build:rename:version',
    'build:rename:class',
    'build:rename:misc',
    'build:remove:testversion',
    'js-admin',
    'js-public',
    gulp.parallel('css-admin', 'css-public', 'css-public-core', 'css-public-grid', 'images-public', 'images-admin'),
    'build:dist',
    'build:update:edd',
    'clean:tmp'
  )(done);

});


/*

Runs all build tasks

Requires:
--tier=""
--release=""

*/
gulp.task('build:free', done => {

  return gulp.series(
    'tests',
    'clean:tmp',
    'clean:free:repo',
    'build:free:copy',
    'build:preprocess',
    'build:rename:plugin',
    'build:rename:version',
    'build:rename:class',
    'build:rename:misc',
    'build:remove:testversion',
    'build:clear:free',
    'js-admin',
    'js-public',
    gulp.parallel('css-admin', 'css-public', 'css-public-core', 'css-public-grid', 'images-public', 'images-admin'),
    'build:free:repo',
    'build:free:repo:copy',
    'clean:tmp'
  )(done);

});


/*

Runs all build tasks

Requires:
--tier=""
--release=""

*/
gulp.task('build:prerelease', done => {

  return gulp.series(
    'tests',
    'clean:tmp',
    'clean:free:repo',
    'build:copy',
    'build:preprocess',
    'build:rename:plugin',
    'build:rename:version',
    'build:rename:class',
    'build:rename:misc',
    'build:remove:testversion',
    'js-admin',
    'js-public',
    gulp.parallel('css-admin', 'css-public', 'css-public-core', 'css-public-grid', 'images-public', 'images-admin'),
    'build:clear:free',
    'build:clear:superfluous',
    'build:zip'
  )(done);

});
