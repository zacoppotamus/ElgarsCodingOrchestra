'use strict';

eco.charts.d3linegraph = function() {
    return {
        title: 'Steam Graph',
        options : {
            width : 1500,
            height : 700,
            margin : {
                top: 100,
                left: 50,
                bottom: 50,
                right: 50
            }
        },

        render: function(data, xValue, yValue, target) {
            //store data before wrapping
            var dd = data;
            //wrap the data
            data = [{"values":data}]; 
            var options = this.options;
            
            var width = options.width,
                height = options.height;
                
            var margin = options.margin;

            var yScale = d3.scale.linear()
                .domain([0, d3.max(dd, function(d) { return +d[yValue]; })])
                .range([height-margin.bottom, margin.top]);
            
            var yAxis = d3.svg.axis()
                .scale(yScale)
                .orient("left")
                .ticks(20);
            
            var yAxisR = d3.svg.axis()
                .scale(yScale)
                .orient("right")
                .ticks(20);
                
            var xScale = d3.scale.linear()
                .domain([0,d3.max(dd, function(d) { return +d[xValue]; })]) 
                .range([margin.left,width-margin.right]);

            var xAxis = d3.svg.axis()
                .scale(xScale)
                .orient("bottom");
                
            var stack = d3.layout.stack()
                .offset("zero")
                .values(function(d) { return d.values; });
                
            var colorScale = d3.scale.category20b();
            
            var area = d3.svg.area()
                .x(function(d) { return xScale(d[xValue]); })
                .y0(function(d) { return yScale(d.y0); })
                .y1(function(d) { return yScale(d.y0 + d[yValue]); });
                
            var svg = target.append("svg")
                .attr("width", width)
                .attr("height", height);
            
            //draw horizontal lines
            svg.selectAll("horizontalGrid")
                .data(yScale.ticks(20))
                .enter()
            .append("line")
                .attr(
                {
                    "x1" : margin.left,
                    "x2" : width - margin.right,
                    "y1" : function(d){ return yScale(d);},
                    "y2" : function(d){ return yScale(d);},
                    "shape-rendering" : "crispEdges",
                    "stroke" : "grey",
                    "stroke-width" : "1px"
                });
            
            svg.selectAll("path")
                .data(stack(data))
            .enter().append("path")
                .attr("class", "layer")
                .attr("d", function(d) { return area(d.values); })
                .style("fill", function() { return colorScale(Math.random())});
            
            //left axis  
            svg.append("g")
                .attr("class", "y axis")
                .call(yAxis)
                .attr("transform", "translate("+margin.left+",0)")
            .append("text")
                .attr("transform", "rotate(-90)")
                .attr("y", -40)
                .attr("x", -height/2)
                .attr("dy", ".21em")
                .style("text-anchor", "end")
                .style("font", "12px Helvetica")
                .text(yValue);
                
            //right axis
            svg.append("g")
                .attr("class", "y axis")
                .call(yAxisR)
                .attr("transform", "translate("+(width-margin.right)+",0)")
            
            svg.append("g")
                .attr("class", "x axis")
                .call(xAxis)
                .attr("transform", "translate(0,"+(height-margin.bottom)+")")
            .append("text")
                .attr("transform", "translate("+(width/2)+",35)")
                .style("text-anchor", "end")
                .style("font", "12px Helvetica")
                .text(xValue);
                
            d3.select("path")
                .on("mousemove", mousemove)
                .on("click", mouseclick);
            
            function mousemove()
            {
                var posX = (d3.mouse(this)[0] - margin.left - 1);
                var posY = (height+margin.top - 131) - d3.mouse(this)[1];
                
                var xmouseScale = d3.scale.linear()
                .range([0, d3.max(dd, function(d) { return +d[xValue]; })])
                .domain([0, width-margin.left-margin.right]);
                
                var ymouseScale = d3.scale.linear()
                .range([0, d3.max(dd, function(d) { return +d[yValue]; })])
                .domain([0, height-margin.top-margin.bottom-3]);
                
                d3.selectAll("[class=steam-text]").remove();
                svg.append("g")
                    .append("text")
                    .attr("x", 25)
                    .attr("y", 50)
                    .attr("class", "steam-text")
                    .attr("fill", "#483D8B")
                    .text(xValue + ": " + Math.round(xmouseScale(posX))
                        + ", " 
                        + yValue + ": " + Math.round(ymouseScale(posY)));
            };

            function mouseclick()
            {
                d3.select(this)
                    .style("fill", function() { return colorScale(Math.random())});
            };
            
        }
    }
}
