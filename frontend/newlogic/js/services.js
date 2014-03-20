'use strict';

// Use shared services here as a way to share data between controllers, or between
// controller/directive

angular.module('eco.services', [])
.factory('dataService', function($http, $q) {
		var dataService = {};
		dataService.currentData = '';
		dataService.selectedDataset = '';
		dataService.selectedVizType;
		dataService.vizOptions = '';

		// get data for the selected dataset
		// TODO check for empty datasets 
		dataService.sayhey = function(name) {
			console.log('Hi, '+name);
		};

		dataService.getData = function(datasetName, username, apikey) {
			// to do: pass apikey and username as parameters
			// this happens asynchronously
			var promise = $http({
				method: 'GET',
				url: 'https://sneeza-eco.p.mashape.com/datasets/' + username + '.' + datasetName+'/data',
				headers: {
					'X-Mashape-Authorization' : apikey
				}
			}).
			success(function(json) {
				console.log('Done getting data from ' + datasetName + ' and injecting to controller');
				dataService.currentData = json.data.results;
			});

			return promise;
		}

/*		dataService.getDatasetNames = function(apiKey) {
			// todo: get user's API key from cookie
			var datasets;
			var promise = $http({
				method: 'GET',
				url: 'https://sneeza-eco.p.mashape.com/datasets',
				headers: {
					'X-Mashape-Authorization' : apiKey
				}
			}).
			success(function(json) {
				// attach the data to the scope
				var datasets = [];
				console.log(json);
				$.each(json.data.datasets, function(key,val) {
					var datasetName = val.name.split('.')[1];
					dataService.datasets.push(datasetName);
				});
			});

			console.log(datasets);
			return datasets;
		};*/

		dataService.getSelectedDataset = function() {
			console.log(this.selectedDataset);
			return this.selectedDataset;
		};

		dataService.getCurrentData = function() {
			console.log(this.currentData);
			return this.currentData;
		};

		dataService.getSelectedVizType = function() {
			console.log(this.selectedVizType);
			return this.selectedVizType;
		};

		

		return dataService;
});