// create module for custom directives
var d3DemoApp = angular.module('d3DemoApp', ['ui.bootstrap']);

// controller
d3DemoApp.controller('AppCtrl', function DropdownCtrl($scope, $http) {
	// different kind of visualizations
	$scope.vizTypes = [
		{'id': 1 , 'name':'Pie Chart'},
		{'id': 2 , 'name':'Bar Chart'},
		{'id': 3 , 'name':'Bubbles'}
	];

	// selected radio button
	$scope.selectedVizType = {id:1};

	// currently selected dataset
	$scope.selectedDataset = '';

	$scope.getDatasetNames = function() {
		$http({
			method: 'GET',
			url: 'http://api.spe.sneeza.me/datasets'
		}).
		success(function(json) {
			// attach the data to the scope
			$scope.datasets = json.data.datasets;

			// clean the error messages
			$scope.error = '';
		}).
		error(function (data, status) {
			if (status === 404) {
				$scope.error = 'Datasets not found';
			} else {
				$scope.error = 'Error: ' + status;
			}
		});
	};

	// get the datasets immediately
	$scope.getDatasetNames();
});