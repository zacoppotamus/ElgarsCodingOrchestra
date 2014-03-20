eco.charts.d3barchart = function() {

	return {
		title: 'Bar Chart',
		
		options : {
			width : 1300,
			height : 600,
			margin : {
				top: 40,
				right: 20,
				bottom: 30,
				left: 80
			}
		},

		render: function(data, xValue, yValue, target) {

			var options = this.options,
				width = options.width,
				height = options.height;

			var margin = {
				top: options.margin.top,
				bottom: options.margin.bottom,
				left: options.margin.left,
				right: options.margin.right
			};

			// count the number of rows in the dataset for xValue.
			// xCount = ...;
			var xScale = d3.scale.ordinal()
				.domain(d3.range(67))
				.rangeRoundBands([0, width], .1);

			// get the max value of the column for yValue.
			// yMax = ...;
			// CHANGE THIS
			var yScale = d3.scale.linear()
				.domain([0, 55])
				.range([height, 0]);

			var colorScale = d3.scale.category20();
			
			var svg = d3.select(target)
				.append("svg")
					.attr("width", width + margin.left + margin.right)
					.attr("height", height + margin.top + margin.bottom)
					.attr("class", "bar-chart")
				.append("g")
					.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

			// make the number of ticks customizable
			var yAxis = d3.svg.axis()
				.scale(yScale)
				.orient("left")
				.ticks(10);

			var bar = svg.selectAll("g")
				.data(data, function(d) {
					return d[yValue];
				})
				.enter()
				.append("g");

			bar.append("rect")
				.attr('x', function(d,i) {
					return xScale(i);
				})
				.attr('y', function(d) {
					return yScale(d[yValue]);
				})
				.attr('height', function(d) {
					return height - yScale(d[yValue]);
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
						return yScale(d[yValue]) - 10;
					})
					.attr("dy", ".35em")
					.attr("height", 10)
					.attr("width", 20)
					.text(function(d) { return d[xValue]; });

			svg.append("g")
					.attr("class", "y axis")
					.call(yAxis)
				.append("text")
					.attr("transform", "rotate(-90)")
					.attr("y", 10)
					.attr("dy", ".21em")
					.style("text-anchor", "end")
					.style("font", "10px Helvetica")
					.text(yValue);

			return this;
		}
	}
};
