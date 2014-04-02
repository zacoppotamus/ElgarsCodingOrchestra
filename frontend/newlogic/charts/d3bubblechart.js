eco.charts.d3bubblechart = function() {
    return {
        title : 'Bubble Chart',

        options : {
            width : 1300,
            height : 800,
            margin : {
				top: 100,
				right: 20,
				bottom: 30,
				left: 80
			}
        },

        render : function(data, xValue, yValue, maxRadius, target) {
            options = this.options;

            var width = options.width,
                height = options.height;
            
            var color = d3.scale.category20b();
            
            var maxElement = d3.max(d3.values(data),function(i){
                return +i[yValue];
            });
            var minElement = d3.min(d3.values(data),function(i){
                return +i[yValue];
            });
            
            var scale = d3.scale.linear()
                    .range([10, height/10])
                    .domain([minElement, maxElement]);
                    
            var scalingFactor = (height/10)/maxElement;
            
            var svg = target.append("svg")
                .attr("class", "bubble-chart");
            
            var force = d3.layout.force()
                .nodes(data)
                .size([width, height])
                .gravity(0.5)
                .charge(-60000/data.length)
                .start();

            var nodes = svg.selectAll(".bubble-chart-node")
                .data(force.nodes())
                .enter()
                .append("g")
                .attr("class", "bubble-chart-node")
                .on("mouseover", mouseover)
                .on("mouseout", mouseout)
                .call(force.drag);
                
            nodes.append("circle")
                .attr("r", function(data)
                {
                    return scale(data[yValue]);
                })
                .attr("fill", function(d, i) 
                {
                    return color(i);
                });
            
            force.on("tick", function()
            {
                nodes.attr("transform", function(d)
                {
                    return "translate(" + d.x + "," + d.y + ")";
                });
            });
            
            function mouseover(d)
            {
                d3.select(this)
                    .select("circle")
                    .transition()
                    .duration(150)
                    .attr("r", function(data)
                    {
                        return scale(data[yValue]) * 1.2;
                    });
                
                d3.selectAll("[class=bubble-text]").remove();
                
                svg.append("g")
					.append("text")
					.attr("x", 25)
					.attr("y", 50)
					.attr("class", "bubble-text")
					.attr("fill", "#483D8B")
                    .text(d[xValue] + ": " + d[yValue]);
            };
            
            function mouseout()
            {
                d3.select("bubble-chart-tooltip").classed("hidden", true);
                
                d3.select(this)
                    .select("circle")
                    .transition()
                    .duration(150)
                    .attr("r", function(data)
                    {
                        return scale(data[yValue]);
                    });
            };
        }
    }
}
