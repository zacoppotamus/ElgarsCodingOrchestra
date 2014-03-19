'use strict';

/* controllers */

// to do: Get data from our api, make it all faster

angular.module('eco.controllers', [])
.controller('ecoCtrl', function ($scope, $http, dataService) {

	/*
		TO DO: If one option is null or two or more are the same throw error.

		When Visualize button is pressed, check that all parameters are != null.

		Maybe put vizTypes in a different file altogether
	*/

	// different kind of visualizations and choices
	// Try different alternatives for decoupling the DOM and controller
	// this can also be a class-function instantiated on $scope.watch so that everything becomes null
	$scope.getParamOptions = function () {
		// choices must have the exact same values as options.
		return [
			{
				'id' : 0, 
				'name' : 'Pie Chart', 
				'choices' : ['values', 'names'],
				'options' : {
					'values' : null,
					'names' : null
				}
			},
			{
				'id': 1,
				'name':'Bar Chart',
				'choices':['xAxis', 'yAxis'],
				'options' : {
					'xAxis' : null,
					'yAxis' : null
				}
			},
			{
				'id': 2,
				'name':'Bubble Chart',
				'choices':['x', 'y', 'maxRadius', 'label'],
				'options' : {
					'x' : null,
					'y' : null,
					'maxRadius' : null,
					'label' : null
				}
			},
			{
				'id': 3,
				'name':'Map',
				'choices':['latitude', 'longitude'],
				'options' : {
					'latitude' : null,
					'longitude' : null
				}

			}
		];
	}

	$scope.vizTypes = $scope.getParamOptions();
	// random shit for testing
	$scope.vizTypes[1].options.xAxis = 'Name';
	$scope.vizTypes[1].options.yAxis = 'TD';
	$scope.vizTypes[1].options.yMax = 70;

	// selected radio button
	$scope.selectedVizType = {id:2};

	// currently selected dataset
	$scope.selectedDataset = '';

	// currently selected field
	$scope.currentField = '';

	// the fields for the selected dataset
	$scope.fields = [];

	$scope.currentData = '';

	// use ben's cookie and username for the time being
	$scope.apiKey = "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd";
	$scope.username = 'benelgar';

	// years for sample vis (nbapayrolls)
	// $scope.years = [];
	// for (var i = 1998; i <= 2017; i++) {
	// 	$scope.years.push(i);
	// }

	// reinitialize $scope.vizTypes here
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

	dataService.sayhey();
	// console.log($scope.currentData);
	// $scope.validParams = true;

	// get the datasets immediately
	$scope.getDatasetNames();
	console.log($scope.datasets);

	// when a new dataset is selected from the dropdown get its fields
	$scope.$watch('selectedDataset', function() {
		$scope.getFields();
		// reinitialize parameter options
		$scope.vizTypes = $scope.getParamOptions();
	});	

	$scope.$watch('selectedVizType.id', function() {
		$scope.validParams = true;
	});

	$scope.$watch('vizTypes', function() {
		$scope.checkParameters();
	})

});