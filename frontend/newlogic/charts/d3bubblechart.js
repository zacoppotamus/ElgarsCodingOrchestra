eco.charts.d3bubblechart = function() {
    return {
        title : 'Bubble Chart',

        options : {
            width : 1300,
            height : 800
        },

        render : function(data, xValue, yValue, maxRadius, target) {
            options = this.options;

            var width = options.width,
                height = options.height;
            
            var color = d3.scale.category20b();
            
            var maxElement = d3.max(d3.values(data),function(i){
                return +i[yValue];
            });
<<<<<<< HEAD
            var minElement = d3.min(d3.values(data),function(i){
                return +i[yValue];
            });
            
            var scale = d3.scale.linear()
                    .range([10, height/10])
                    .domain([minElement, maxElement]);
                    
            var scalingFactor = (height/10)/maxElement;
            
            var svg = target.append("svg")
                .attr("class", "bubble-chart");
=======

            // radius scale
            var rScale = d3.scale.linear()
                .domain([0, maxElement])
                .range([4.5, height/10]);
            
            var svg = target.append("svg")
                .attr("class", "bubble-chart");
            
            var div = target.append("div")
                .attr("class", "hidden")
                .attr("id", "bubble-chart-tooltip");
                
            div.append("p")
                .attr("id", xValue);
            div.append("p")
                .attr("id", yValue);
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
            
            var force = d3.layout.force()
                .nodes(data)
                .size([width, height])
                .gravity(0.5)
<<<<<<< HEAD
                .charge(-60000/data.length)
=======
                .charge(-2000)
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
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
<<<<<<< HEAD
                    return scale(data[yValue]);
=======
                    return rScale(data[yValue]);
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
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
<<<<<<< HEAD
=======
                var xPos = d.x - 50 - 5;
                var yPos = d.y - ((maxElement-rScale(d[yValue]))+maxElement)*1.5;
                //Update the bubble-chart-tooltip position and value
                d3.select("#bubble-chart-tooltip")
                  .style("left", xPos + "px")
                  .style("top", yPos + "px")
                  .select("#"+xValue)
                  .text(d[xValue]);
                d3.select("#bubble-chart-tooltip")
                  .style("left", xPos + "px")
                  .style("top", yPos + "px")
                  .select("#"+yValue)
                  .text(d[yValue]);
                d3.select("#bubble-chart-tooltip").classed("hidden", false);
                
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
                d3.select(this)
                    .select("circle")
                    .transition()
                    .duration(150)
                    .attr("r", function(data)
                    {
<<<<<<< HEAD
                        return scale(data[yValue]) * 1.2;
=======
                        return rScale(data[yValue]) * 1.2;
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
                    });
                console.log(d);
                
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
<<<<<<< HEAD
                        return scale(data[yValue]);
=======
                        return rScale(data[yValue]);
>>>>>>> 2fa66cde963463f19764b9b8f5ae1b20b89588bf
                    });
            };
        }
    }
}
