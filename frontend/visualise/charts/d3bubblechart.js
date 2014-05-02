eco.charts.d3bubblechart = function() {
    return {
        title : 'Bubble Chart',

        options : {
            width : 1500,
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
                    .range([10, height/15])
                    .domain([minElement, maxElement]);
                    
            var scalingFactor = (height/10)/maxElement;
            
            var svg = target.append("svg")
                .attr("class", "bubble-chart");
            
            var force = d3.layout.force()
                .nodes(data)
                .size([width, height])
                .gravity(0.01)
                .charge(-1000/data.length)
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
                callCollisions();
                
                svg.selectAll("circle")
                  .attr("cx", function(d) { return d.x; })
                  .attr("cy", function(d) { return d.y; });
            });
            
            svg.on("mousemove", function()
            {
                force.resume();
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
                
                callCollisions(d);
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
            
            function collide(node,ex) 
            {
                var r = scale(node[yValue]) * ex + 20,
                    nx1 = node["x"] - r,
                    nx2 = node["x"] + r,
                    ny1 = node["y"] - r,
                    ny2 = node["y"] + r;
                return function(quad, x1, y1, x2, y2) 
                {
                    if (quad.point && (quad.point !== node)) 
                    {
                        var x = node["x"] - quad.point.x,
                            y = node["y"] - quad.point.y,
                            l = Math.sqrt(x * x + y * y),
                            r = (scale(node[yValue]) + scale(quad.point[yValue])) * ex;
                        if (l < r) 
                        {
                            l = (l - r) / l * .5;
                            node["x"] -= x *= l;
                            node["y"] -= y *= l;
                            quad.point.x += x;
                            quad.point.y += y;
                        }
                    }
                    return x1 > nx2 || x2 < nx1 || y1 > ny2 || y2 < ny1;
                };
            };
            
            function callCollisions(selected)
            {
                var nn = nodes[0];
                var n = nn.length;
                var i = -1, j = -1;
                var cords = new Array();
                
                function f(d,j)
                {
                    cords[j] = d[j]["__data__"];
                };
                while (++j < n) f(nn,j);
                
                var q = d3.geom.quadtree(cords);
                while (++i < n)
                {
                    if (selected != null)
                    {
                        if (cords[i].index == selected.index)
                            q.visit(collide(cords[i],1.1))
                        else
                            q.visit(collide(cords[i],1));
                    }
                    else
                        q.visit(collide(cords[i],1));
                };
            }
        }
    }
}
