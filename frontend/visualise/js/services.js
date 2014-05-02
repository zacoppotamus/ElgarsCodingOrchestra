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

        dataService.getData = function(datasetName, apikey) {
            // this happens asynchronously
            var promise = $http({
                method: 'GET',
                url: 'https://sneeza-eco.p.mashape.com/datasets/'+datasetName+'/data',
                headers: {
                    'X-Mashape-Authorization' : apikey
                }
            }).
            success(function(json) {
                dataService.currentData = json.data.results;
            });

            return promise;
        }

        dataService.getSelectedDataset = function() {
            return this.selectedDataset;
        };

        dataService.getCurrentData = function() {
            return this.currentData;
        };

        dataService.getSelectedVizType = function() {
            return this.selectedVizType;
        };

        

        return dataService;
});