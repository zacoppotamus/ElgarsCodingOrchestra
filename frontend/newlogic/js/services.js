'use strict';

angular.module('eco.services', [])
.factory('dataService', function($http, $q) {
		return {
			// get data for the selected dataset
			// TODO check for empty datasets 
			sayhey : function() {
				console.log('Bla!');
			}
		}
	})

/*	getData : function() {
				$http({
					method: 'GET',
					url: 'https://sneeza-eco.p.mashape.com/datasets/benelgar.nflqb2013'+'/data',
					headers: {
						'X-Mashape-Authorization' : "EU6h9H8BUXELDmfO1Mbh0jLasSQxrAZd"
					}
				}).
				success(function(json) {
					return json.data.results;
				});
			}
		}*/