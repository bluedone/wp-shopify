/*

Initializing

*/

import gulp from "gulp";
import config from "../config";

gulp.task('default', done => {

  if (config.isBuilding) {
    gulp.series('js', 'css-admin', 'css-public', 'css-public-core', 'css-public-grid', 'images-public', 'images-admin');

  } else {
    gulp.series( gulp.parallel('js', 'css-admin', 'css-public', 'css-public-core', 'css-public-grid'), 'server', 'watch' )(done);
  }

});
