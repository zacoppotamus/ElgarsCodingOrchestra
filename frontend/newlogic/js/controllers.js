'use strict';

/* controllers */

angular.module('eco.controllers', [])
.controller('ecoCtrl', function ($scope, $http) {
	// different kind of visualizations
	$scope.vizTypes = [
		{'id': 1 , 'name':'Pie Chart'},
		{'id': 2 , 'name':'Bar Chart'},
		{'id': 3 , 'name':'Bubble Chart'},
		{'id': 4 , 'name':'Map'},
	];

	// selected radio button
	$scope.selectedVizType = {id:2};

	// currently selected dataset
	$scope.selectedDataset = '';

	// $scope.datasets = [
	// 	'nysubway',
	// 	'23nations',
	// 	'nbapayrolls',
	// 	'bristolairquality',
	// 	'londoncrimerate'
	// ];

	// years for sample vis (nbapayrolls)
	$scope.years = [];
	for (var i = 1998; i <= 2017; i++) {
		$scope.years.push(i);
	}

	// the fields for the selected dataset
	$scope.fields = [];

	// use ben's cookie and username for the time being
	$scope.apiKey = "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd";
	$scope.username = 'benelgar';

	// test json response when passing header
	$scope.getFields = function() {
		$http({
			method: 'GET',
			url: 'https://sneeza-eco.p.mashape.com/datasets/'+$scope.username+$scope.selectedDataset+'/data',
			headers: {
				'X-Mashape-Authorization' : $scope.apiKey
			}
		}).
		success(function(json) {
			console.log(json);
			if(!json.data["rows"]===0) {
				$.each(json.data.results[0], function(key, val) {
					console.log(key);
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
		console.log('api is: '+$scope.apiKey);

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
			console.log(json.data);
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
	$scope.$watch('selectedDataset', $scope.getFields)

	// parse the fields for the selected dataset
/*	$scope.getFields = function() {

	}*/

	// get the datasets immediately
	$scope.getDatasetNames();
	// getFields();
});