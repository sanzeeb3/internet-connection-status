/* jshint node:true */
module.exports = function( grunt ){
	'use strict';

	grunt.initConfig({

		// Store project settings.
		pkg: grunt.file.readJSON( 'package.json' ),

		// Generate POT files.
		makepot: {
			options: {
				type: 'wp-plugin',
				domainPath: 'languages',
				potHeaders: {
					'report-msgid-bugs-to': '',
					'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
				}
			},
			dist: {
				options: {
					potFilename: 'intenet-connection-status.pot',
					exclude: [
						'vendor/.*'
					]
				}
			}
		},

	});

	// Load NPM tasks to be used here
	grunt.loadNpmTasks( 'grunt-wp-i18n' );
	grunt.registerTask( 'i18n', [
		'makepot'
	]);
};
