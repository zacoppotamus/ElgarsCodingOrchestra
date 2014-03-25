'use strict';

eco.charts.d3piechart = function() {

	return {
		title: 'Pie Chart',

		options : {
			width : 1000,
			height : 600,
			margin : {
				top: 40,
				left: 80
			}
		},

		render: function(data, xValue, yValue, target) {
			var options = this.options;

			var width = options.width,
					height = options.height,
					radius = Math.min(width, height)/2;

			var color = d3.scale.category20();

			var arc = d3.svg.arc()
				.outerRadius(radius - 10)
				.innerRadius(70);

			var pie = d3.layout.pie()
				.sort(null)
				.value(function(d) { return d[yValue]; });

			var svg = target.append("svg")
					.attr("width", width)
					.attr("height", height)
					.attr("class", "pie-chart")
				.append("g")
					.attr("transform", "translate(" + width / 2 + "," + height / 2 + ")");

			data.forEach(function(d) {
				d[yValue] = +d[yValue];
			});

			var g = svg.selectAll(".pie-chart-arc")
					.data(pie(data))
				.enter().append("g")
					.attr("class", "pie-chart-arc");

			g.append("path")
				.attr("d", arc)
				.style("fill", function(d) { return color(d.data[xValue]); });

			g.append("text")
				.attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; })
				.attr("dy", ".35em")
				.style("text-anchor", "middle")
				.text(function(d) { return d.data[xValue]; });
		}
	}
}