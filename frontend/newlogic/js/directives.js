'use strict';

/* directives */

// camelCase directive names are transformed to dashes
angular.module('eco.directives', [])
.directive('d3Visualization', function() {

	// constants
	var margin = {top:20, right:20, bottom:30, left:40},
		width  = 1100 - margin.left - margin.right,
		height = 500 - margin.top - margin.bottom;

	return {
		restrict: 'E',
		terminal: true,
		scope: {
			year: '='
		},
		link: function (scope, element, attrs) {
			var svg = d3.select(element[0])
				.append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
				.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			scope.$watch('year', function (newVal, oldVal) {

				// clear everything when value changes
				svg.selectAll('*').remove();
				console.log(newVal);

				if (!newVal) {
					return;
				}

				d3.json('./samples/nba_payrolls.json', function(error, json){
					// number of teams
					var teamNo = 30;
					var currentYear = newVal;
					var dataset = json[currentYear];
					var barWidth = width/teamNo;

					// get max team payroll for this year
					var maxPayroll = d3.max(d3.values(json[currentYear]), function(teamnode) {
						return teamnode.payroll;
					});

					var xScale = d3.scale.ordinal()
						.domain(d3.range(teamNo))
						.rangeRoundBands([0, width], .1);

					var yScale = d3.scale.linear()
						.domain([0, maxPayroll])
						.range([height, 0]);

					var colorScale = d3.scale.category20();

					var bar = svg.selectAll("g")
						.data(d3.values(dataset), function(d) {
							return d.payroll;
						})
						.enter()
						.append("g");

					var yAxis = d3.svg.axis()
						.scale(yScale)
						.orient("left")
						.ticks(10);

					svg.append("g")
							.attr("class", "y axis")
							.call(yAxis)
						.append("text")
							.attr("transform", "rotate(-90)")
							.attr("y", 6)
							.attr("dy", ".21em")
							.style("text-anchor", "end")
							.style("font", "10px Helvetica")
							.text("Payroll");

					bar.append("rect")
						.attr('x', function(d,i) {
							return xScale(i);
						})
						.attr('y', function(d) {
							return yScale(d.payroll);
						})
						.attr('height', function(d) {
							return height - yScale(d.payroll);
						})
						.attr('width', xScale.rangeBand())
						.attr("fill", function(d,i) {
							return colorScale(i);
						});


					// add labels
					bar.append("text")
						.attr("text-anchor", "middle")
						.attr("x", function(d, i) {
							return xScale(i) + xScale.rangeBand() / 2;
						})
						.attr("y", function(d) {
							return yScale(d.payroll) - 10;
						})
						.attr("dy", ".35em")
						.attr("height", 10)
						.attr("width", 20)
						.text(function(d) { return d['team']; });

				});

			});


		}
	}
})