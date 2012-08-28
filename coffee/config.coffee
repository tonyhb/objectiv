require.config({

	# Our entry point for main app. The path is taken from data-main in the
	# template.
	deps: ['main'],

	# Libraries will be using
	paths: {
		libs: '/assets/js/libs',

		jquery: '/assets/js/libs/jquery-1.8.0.min',
		underscore: '/assets/js/libs/underscore-min',
		backbone: '/assets/js/libs/backbone-min',
		modernizr: '/assets/js/libs/modernizr',
	},

	# These are non-AMD libraries.
	shim: {
		backbone: {
			deps: ['jquery', 'underscore'],
			exports: 'Backbone'
		},
		modernizr: {
			exports: 'Modernizr'
		}
	}
})
