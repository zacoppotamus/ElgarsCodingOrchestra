'use strict';

eco.charts.d3piechart = function() {

	return {
		title: 'Pie Chart',

		options : {
			width : 1400,
			height : 800,
			margin : {
				top: 30,
				left: 80
			}
		},

		render: function(data, xValue, yValue, target) {
			var options = this.options;

			var width = options.width,
					height = options.height,
					maxRadius = Math.min(width, height)/2;

			var color = d3.scale.category20b();
			
			//holds whether an element is being viewed
			var viewToggle = false;

			var arc = d3.svg.arc()
				.outerRadius(maxRadius)
				.innerRadius(0);

			var pie = d3.layout.pie()
				.sort(null)
				.value(function(d) { return d[yValue]; });

			var svg = target.append("svg")
					.attr("width", width + options.margin.top)
					.attr("height", height + options.margin.left)
					.attr("class", "pie-chart")
				.append("g")
					.attr("transform", "translate(" 
					    + ((width / 2) + options.margin.left) 
					    + "," 
					    + ((height / 2) + options.margin.top) 
					    + ")");

			data.forEach(function(d) {
				d[yValue] = +d[yValue];
			});

			var g = svg.selectAll(".pie-chart-arc")
					.data(pie(data))
				.enter().append("g")
					.attr("class", "pie-chart-arc");

			g.append("path")
				.attr("d", arc)
				.style("fill", function(d) { return color(d.data[xValue]); })
				.attr("opacity", 0.9)
				.on("mouseover", mouseover)
				.on("mouseout", mouseout)
				.on("click", mouseclick);

			g.append("text")
				.attr("transform", function(d) 
				{   
                    d.outerRadius = maxRadius;
                    d.innerRadius = maxRadius/2;
                    return "translate(" + arc.centroid(d) + ")rotate(" + angle(d) + ")";
                })
				.attr("dy", ".35em")
				.style("text-anchor", "middle")
				.text(function(d) { return d.data[xValue]; });
			
			function mouseover(d)
			{
			    d3.selectAll("[class=pie-header-text]").remove();
                
                svg.append("g")
					.append("text")
					.attr("x", 25)
					.attr("y", 50)
					.attr("class", "pie-header-text")
					.attr("fill", "#483D8B")
					.attr("transform", "translate(-" 
					    + (width / 2 + options.margin.left) 
					    + ",-" 
					    + (height / 2 + options.margin.top) 
					    + ")")
                    .text(d["data"][xValue] + ": " + d["data"][yValue]);
                
                d3.select(this)
                    .transition()
                    .duration(100)
                    .attr("opacity", 1);
			}
			
			function mouseout(d)
            {
                if (!viewToggle)
                {
                    d3.selectAll("path")
                        .transition()
                        .duration(200)
                        .attr("opacity", 0.9);
                }
            }
            
            function mouseclick(d)
            {
                //select all but the selected element
                var selectedElement = this;
                d3.selectAll("path")
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
				
		    // Calculates the arc angle then converts from radians to degrees.
            function angle(d) 
            {
              var a = (d.startAngle + d.endAngle) * 90 / Math.PI - 90;
              return a > 90 ? a - 180 : a;
            }
		}
	}
}
