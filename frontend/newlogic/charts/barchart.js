eco.charts.barchart = function() {

	return {
		title: 'Bar Chart',
		description: 'The simplest and most used tool, the reliable barchart',
		model: {
			xAxis : {
				value : []
			},

			yAxis : {
				value : []
			},

			yMax : {
				value : []
			}
		},

		options : {
			width : {
				value: 1100,
				title: 'Width',
				type: 'number'
			},
			height : {
				value: 600,
				title: 'Height',
				type: 'number'
			},
			margin : {
				top: 40,
				right: 20,
				bottom: 30,
				left: 80
			}
		},

		// maybe make label a separate field
		render: function(data, yName, xName, target) {
			// assume that data is a json file, meaning the user uses d3.json
			// in the directive and then passes on the dataset here, along with
			// the names for the two axe values (yName, xName).
			var model = this.model,
				options = this.options,
				width = options.width.value,
				height = options.height.value;

			var margin = {
				top: options.margin.top,
				bottom: options.margin.bottom,
				left: options.margin.left,
				right: options.margin.right
			};

			var svg. = d3.select(target)
				.append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.attr("class", "bar-chart")
				.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			d3.json(data, function(error, json) {
				// count the number of rows in the dataset for xName.
				// xCount = ...;
				var xScale = d3.scale.ordinal()
					.domain(d3.range(xCount))
					.rangeRoundBands([0, width], .1);

				// get the max value of the column for yName.
				// yMax = ...;
				var yScale = d3.scale.linear()
					.domain([0, yMax])
					.range([height, 0]);

				var colorScale = d3.scale.category20();

				var bar = svg.selectAll("g")
					.data(d3.values(json), function(d) {
						return d[yName];
					})
					.enter()
					.append("g");

				// make the number of ticks customizable
				var yAxis = d3.svg.axis()
					.scale(yScale)
					.orient("left")
					.ticks(10);

				svg.append("g")
						.attr("class", "y axis")
						.call(yAxis)
					.append("text")
						.attr("transform", "rotate(-90)")
						.attr("y", 10)
						.attr("dy", ".21em")
						.style("text-anchor", "end")
						.style("font", "10px Helvetica")
						.text(yName);

				bar.append("rect")
					.attr('x', function(d,i) {
						return xScale(i);
					})
					.attr('y', function(d) {
						return yScale(d[yName]);
					})
					.attr('height', function(d) {
						return height - yScale(d[yName]);
					})
					.attr('width', xScale.rangeBand())
					.attr("fill", function(d,i) {
						return colorScale(i);
					});

				// add labels
				bar.append("text")
					.attr("text", "middle")
					.attr("x", function(d, i) {
							return xScale(i) + xScale.rangeBand() / 2;
						})
						.attr("y", function(d) {
							return yScale(d.payroll) - 10;
						})
						.attr("dy", ".35em")
						.attr("height", 10)
						.attr("width", 20)
						.text(function(d) { return d[xName]; });
			});

			return this;
		}
	}
};
