'use strict';

angular.module('eco', [
	'eco.controllers',
	'eco.directives',
	'ngRoute',
	'ui.bootstrap'
])

.config(function($routeProvider, $locationProvider) {
	// configure routes
	$routeProvider

		// route for the final visualization
		.when('/visualization', {
			templateUrl: 'visualization.html',
			controller: 'ecoCtrl'
		})

		// is '/' when starting server within newlogic/
		.when('/', {
			templateUrl: 'customize.html',
			controller: 'ecoCtrl'
		});

	$locationProvider.html5Mode(true);
});
