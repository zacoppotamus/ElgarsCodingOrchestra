eco.charts.d3barchart = function() {

	return {
		title: 'Bar Chart',
		
		options : {
			width : 1400,
			height : 600,
			margin : {
				top: 70,
				right: 20,
				bottom: 140,
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
			
			//holds whether an element is being viewed
			var viewToggle = false;

			var xCount = 0;
			for (k in data) if (data.hasOwnProperty(k)) xCount++;
			console.log(xCount);

			// count the number of rows in the dataset for xValue.
			// xCount = ...;
			var xScale = d3.scale.ordinal()
				.domain(d3.range(xCount))
				.rangeRoundBands([0, width], .1);

			// get the max value of the column for yValue.
			// yMax = ...;
			// CHANGE THIS
			var yScale = d3.scale.linear()
				.domain([0, d3.max(data, function(d) { return +d[yValue]; })])
				.range([height, 0]);

			var colorScale = d3.scale.category20b();
			
			var svg = target
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

			var xAxis = d3.svg.axis()
				.scale(xScale)
				.orient("bottom");

			var bar = svg.selectAll("g")
				.data(data, function(d) {
					return d[yValue];
				})
				.enter()
				.append("g");

			bar.append("rect")
				.attr
				({
				    x : function(d,i) 
				        {
					        return xScale(i);
				        },
				    y : function(d) 
				        {
					        return yScale(d[yValue]);
				        },
				    height: function(d) 
				        {
					        return height - yScale(d[yValue]);
				        },
				    width: xScale.rangeBand(),
				    fill: function(d,i) 
				        {
					        return colorScale(i);
				        },
				    opacity: 0.9
			    })
                .on
                ({
                    mouseover : mouseover,
                    mouseout : mouseout,
                    click : mouseclick
                });
				
			// add labels
			bar.append("text")
			    .attr("class", "bar-text")
				.attr("text", "middle")
				.attr("dy", ".35em")
				.attr("height", 10)
				.attr("width", 20)
				.attr('transform', function(d,i) 
				{
				    return d3.transform('translate(' 
				        + (xScale(i) + (xScale.rangeBand()/2))
				        + ',' 
				        + (height + 5)
				        + ') rotate(90)').toString();
				})
				.text(function(d) { console.log(d);return d[xValue]; });

			svg.append("g")
					.attr("class", "y axis")
					.call(yAxis)
				.append("text")
					.attr("transform", "rotate(-90)")
					.attr("y", -40)
		            .attr("x", -height/2)
					.attr("dy", ".21em")
					.style("text-anchor", "end")
					.style("font", "12px Helvetica")
					.text(yValue);

            function mouseover(d)
            {
                var xPos = d3.select(this).attr("x");
                var yPos = d3.select(this).attr("y");
                
                //text showing x and y values
                d3.selectAll("[class=bar-header-text]").remove();
                
                svg.append("g")
					.append("text")
					.attr("x", 25 - margin.left)
					.attr("y", 50 - margin.top)
					.attr("class", "bar-header-text")
					.attr("fill", "#483D8B")
                    .text(d[xValue] + ": " + d[yValue]);
                    
                d3.select(this)
                    .transition()
                    .duration(100)
                    .attr("opacity", 1);
            }
            
            function mouseout(d)
            {
                if (!viewToggle)
                {
                    d3.selectAll("rect")
                        .transition()
                        .duration(200)
                        .attr("opacity", 0.9);
                }
            }
            
            function mouseclick(d)
            {
                //select all but the selected element
                var selectedElement = this;
                d3.selectAll("rect")
                    .filter(function(d) 
                    {
                        return (this !== selectedElement);
                    })
                    .transition()
                    .duration(150)
                    .attr("opacity", 0.4);
                    
                viewToggle = !viewToggle;
                if (!viewToggle) mouseout(d);
            }

			return this;
		}
	}
};
