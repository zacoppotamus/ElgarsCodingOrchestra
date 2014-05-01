'use strict';

/* controllers */

// to do: Get data from our api, make it all faster

angular.module('eco.controllers', [])
.controller('ecoCtrl', function ($scope, $http, dataService) {

	/*
		TO DO: If one option is null or two or more are the same throw error.

		When Visualize button is pressed, check that all parameters are != null.
	*/

	// selected radio button
	$scope.selectedVizType = {id:1};

	// has the user requested to visualise a certain dataset?
	if (window.location.search == '') {
		$scope.selectedDataset = '';
	} 
	else {
		$scope.selectedDataset = window.location.search.slice(1).split('=')[1];
	}

	// the fields for the selected dataset
	$scope.fields = [];

	// current user's api key
	$scope.apiKey = apiKey;

	// reinitialize $scope.vizTypes here
	$scope.getFields = function() {
		$http({
			method: 'GET',
			url: 'https://sneeza-eco.p.mashape.com/datasets/'+$scope.selectedDataset+'/data',
			headers: {
				'X-Mashape-Authorization' : $scope.apiKey
			}
		}).
		success(function(json) {
			$scope.fields = [];
			if(json.data["rows"] != undefined) {
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
				// var datasetName = val.name.split('.')[1];
				var datasetName = val.name;
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

	// check to see that no parameter is empty
	$scope.checkParameters = function() {
		// if ($scope.valid == false) {
		// 	alert("Invalid options");
		// }
		$scope.validParams = true;
		for (var field in $scope.vizTypes[$scope.selectedVizType.id].options)
		{
			if ($scope.vizTypes[$scope.selectedVizType.id].options[field] === null) {
				$scope.validParams = false;
				console.log("Invalid parameters!");
				return;
			}
		}
	};

	// send visualization options to the service and vis type.
	// Do this when the 'visualize' is clicked but in the future, 
	// validate the options made accordingly.
	$scope.sendVizOptionsToService = function() {
		console.log('Sending options to service!');
		dataService.selectedVizType = $scope.selectedVizType.id;
		dataService.vizOptions = $scope.vizTypes;
	}

	// $scope.validParams = true;

	// get the datasets immediately
	$scope.getDatasetNames();

	// when a new dataset is selected from the dropdown get its fields
	$scope.$watch('selectedDataset', function() {
		if ($scope.selectedDataset != '')
		{
			$scope.getFields();
			dataService.selectedDataset = $scope.selectedDataset;
			dataService.getSelectedDataset();
			$scope.currentData = dataService.getData($scope.selectedDataset, $scope.apiKey);
		}
		
		// reinitialize parameter options
		$scope.vizTypes = eco.charts();
	});

	$scope.$watch('selectedVizType.id', function() {
		$scope.validParams = true;
	});

	$scope.$watch('vizTypes', function() {
		$scope.checkParameters();
	})

});