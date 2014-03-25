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
            
            var svg = d3.select("body").append("svg")
                .attr("class", "bubble-chart");
            
            var div = d3.select("body").append("div")
                .attr("class", "hidden")
                .attr("id", "bubble-chart-tooltip");
                
            div.append("p")
                .attr("id", xValue);
            div.append("p")
                .attr("id", yValue);
            
            //TODO change charge to be dependant on radius
            var force = d3.layout.force()
                .nodes(data)
                .size([width/1.5, height/1.5])
                .gravity(0.5)
                .charge(-maxElement*100)
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
                    return data[yValue];
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
                var xPos = d.x - 50 - 5;
                var yPos = d.y - ((maxElement-d[yValue])+maxElement)*1.5;
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
                
                d3.select(this)
                    .select("circle")
                    .transition()
                    .duration(150)
                    .attr("r", function(data)
                    {
                        return data[yValue] * 1.2;
                    });
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
                        return data[yValue];
                    });
            };
        }
    }
}