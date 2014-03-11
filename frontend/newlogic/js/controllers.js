'use strict';

/* controllers */

// to do: Get data from our api, make it all faster

angular.module('eco.controllers', [])
.controller('ecoCtrl', function ($scope, $http) {

	// different kind of visualizations and choices
	$scope.vizTypes = [
		{'id': 0, 'name':'Pie Chart', 'choices':['Values', 'Names']},
		{'id': 1, 'name':'Bar Chart', 'choices':['X-Axis', 'Y-Axis', 'Y Max']},
		{'id': 2, 'name':'Bubble Chart', 'choices':['X', 'Y', 'Max Radius', 'Label']},
		{'id': 3, 'name':'Map', 'choices':['Latitude', 'Longitude']}
	];

	// selected radio button
	$scope.selectedVizType = {id:2};

	// currently selected dataset
	$scope.selectedDataset = '';

	// currently selected field
	$scope.currentField = '';

	// the fields for the selected dataset
	$scope.fields = [];

	// use ben's cookie and username for the time being
	$scope.apiKey = "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd";
	$scope.username = 'benelgar';

	// years for sample vis (nbapayrolls)
	$scope.years = [];
	for (var i = 1998; i <= 2017; i++) {
		$scope.years.push(i);
	}

	// test json response when passing header
	$scope.getFields = function() {
		$http({
			method: 'GET',
			url: 'https://sneeza-eco.p.mashape.com/datasets/'+$scope.username+'.'+$scope.selectedDataset+'/data',
			headers: {
				'X-Mashape-Authorization' : $scope.apiKey
			}
		}).
		success(function(json) {
			$scope.fields = [];
			if(json.data["rows"]!=0) {
				$.each(json.data["results"][0], function(key, val) {
					if (key!='_id') {
						$scope.fields.push(key);
					}
				});
			}
			else {
				return null;
			};
		})
	}

	// (don't have this be a function so it only runs once)
	$scope.getDatasetNames = function() {
		// todo: get user's API key from cookie
		$http({
			method: 'GET',
			url: 'https://sneeza-eco.p.mashape.com/datasets',
			headers: {
				'X-Mashape-Authorization' : $scope.apiKey
			}
		}).
		success(function(json) {
			// attach the data to the scope
			$scope.datasets = [];
			$.each(json.data.datasets, function(key,val) {
				var datasetName = val.name.split('.')[1];
				$scope.datasets.push(datasetName);
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

	// when a new dataset is selected from the dropdown get its fields
	$scope.$watch('selectedDataset', $scope.getFields, true)

	// get the datasets immediately
	$scope.getDatasetNames();
});