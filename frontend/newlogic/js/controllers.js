'use strict';

/* controllers */

angular.module('eco.controllers', [])
.controller('ecoCtrl', function ($scope, $http) {
	// different kind of visualizations
	$scope.vizTypes = [
		{'id': 1 , 'name':'Pie Chart'},
		{'id': 2 , 'name':'Bar Chart'},
		{'id': 3 , 'name':'Bubbles'}
	];

	// selected radio button
	$scope.selectedVizType = {id:2};

	// currently selected dataset
	$scope.selectedDataset = '';
	$scope.datasets = [
		'nysubway',
		'23nations',
		'nbapayrolls'
	];

	// selected year (model)
	// $scope.selectedYear = '';

	$scope.years = [];
	for (var i = 1998; i <= 2017; i++) {
		$scope.years.push(i);
	}

	$scope.getDatasetNames = function() {
		$http({
			method: 'GET',
			url: 'http://api.spe.sneeza.me/datasets'
		}).
		success(function(json) {
			// attach the data to the scope
			$scope.datasets = [];
			$.each(json.data.datasets, function(key,val) {
				$scope.datasets.push(val.name);
				console.log("bla")
			});

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
	// $scope.getDatasetNames();
});