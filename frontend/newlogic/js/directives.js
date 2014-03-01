'use strict';

/* directives */

// camelCase directive names are transformed to dashes
angular.module('eco.directives', [])
.directive('d3Visualization', function() {

	// constants
	var margin = 20,
		width  = 960,
		height = 500 - margin;

	return {
		restrict: 'E',
		terminal: true,
		scope: {
			year: '='
		},
		link: function (scope, element, attrs) {
			var svg = d3.select(element[0])
				.append("svg")
					.attr("width", width)
					.attr("height", height + margin + 100);

			scope.$watch('year', function (newVal, oldVal) {

				// clear everything when value changes
				svg.selectAll('*').remove();
				console.log(newVal);

				if (!newVal) {
					return;
				}

				d3.json('/samples/nba_payrolls.json', function(error, json){
					var currentYear = newVal;
					var dataset = json[currentYear];
					console.log(dataset);
					var colorScale = d3.scale.category20();
					var yScale = d3.scale.linear()
						.domain([0, d3.max(dataset)])
						.range([0, height]);

					var xScale = d3.scale.ordinal()
						.domain(d3.range(dataset.length))
						.rangeRoundBands([0, width], 0.05);

					console.log(newVal);

					console.log('bla');
					svg.selectAll("rect")
						.data(dataset)
						.enter()
						.append('rect')
						.attr('x', function(d,i) {
							return xScale(i);
						})
						.attr('y', function(d) {
							return yScale(d.payroll);
						})
						.attr("fill", function(d,i) {
							return colorScale(i);
						});
				});

			});


		}
	}
})