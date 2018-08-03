var gulp = require( 'gulp' );
var del = require( 'del' );
var run = require( 'gulp-run' );

gulp.task( 'run:build', function () {
	return run( 'npm run build' ).exec();
} )

gulp.task( 'bundle', function () {
	return gulp.src( [
		'**/*',
		'!node_modules/**/*',
		'!js/blocks/**/*',
		'!js/src/**/*',
		'!js/tests/**/*',
		'!js/coverage/**/*',
		'!package/**/*',
	] )
		.pipe( gulp.dest( 'package' ) );
} );

gulp.task( 'remove:bundle', function () {
	return del( [
		'package',
	] );
} );

gulp.task( 'run:readme', function () {
	return run( 'mv package/trunk/readme.txt package/readme.txt' ).exec();
} )

gulp.task( 'clean:bundle', function () {
	return del( [
		'package/node_modules',
		'package/js/blocks',
		'package/js/src',
		'package/tests',
		'package/trunk',
		'package/coverage',
		'package/package',
		'package/gulpfile.js',
		'package/Makefile',
		'package/README.md',
		'package/package*.json',
		'package/phpunit.xml.dist',
		'package/webpack.config.js',
	] );
} );

gulp.task( 'default', gulp.series( 'remove:bundle', 'run:build', 'bundle', 'run:readme', 'clean:bundle' ) );