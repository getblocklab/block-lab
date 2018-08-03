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
		.pipe( gulp.dest( 'package/prepare' ) );
} );

gulp.task( 'remove:bundle', function () {
	return del( [
		'package',
	] );
} );

gulp.task( 'wporg:prepare', function() {
	return run( 'mkdir -p package/assets package/trunk').exec();
} )

gulp.task( 'wporg:assets', function() {
	return run( 'mv package/prepare/assets/wporg/*.* package/assets' ).exec();
} )

gulp.task( 'wporg:readme', function() {
	return run( 'mv package/prepare/trunk/readme.txt package/trunk/readme.txt' ).exec();
} )

gulp.task( 'wporg:trunk', function() {
	return run( 'mv package/prepare/* package/trunk' ).exec();
} )

gulp.task( 'clean:bundle', function () {
	return del( [
		'package/trunk/assets/wporg',
		'package/trunk/coverage',
		'package/trunk/js/blocks',
		'package/trunk/js/src',
		'package/trunk/node_modules',
		'package/trunk/tests',
		'package/trunk/trunk',
		'package/trunk/gulpfile.js',
		'package/trunk/Makefile',
		'package/trunk/package*.json',
		'package/trunk/phpunit.xml.dist',
		'package/trunk/README.md',
		'package/trunk/webpack.config.js',
		'package/prepare',
	] );
} );

gulp.task( 'default', gulp.series(
	'remove:bundle',
	'run:build',
	'bundle',
	'wporg:prepare',
	'wporg:assets',
	'wporg:readme',
	'wporg:trunk',
	'clean:bundle'
) );