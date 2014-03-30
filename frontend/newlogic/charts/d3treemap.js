eco.charts.d3treemap = function() {
	return {
		title: 'Treemap',
		
		options : {
			width : 1400,
			height : 700,
			margin : {
				top: 50,
				right: 50,
				bottom: 50,
				left: 50
			}
		},

		render: function (data, xValue, yValue, target) {
            var data = {name: xValue,
                "children": data};
            var options = this.options;
            var margin = options.margin;
            var width = options.width - margin.left - margin.right,
                height = options.height - margin.top - margin.bottom;
            
            var maxElement = d3.max(d3.values(data.children),function(i){ return +i[yValue] });
            var minElement = d3.min(d3.values(data.children),function(i){ return +i[yValue] });
            
            var textScale = d3.scale.linear()
                .range([12,36])
                .domain([minElement, maxElement]);
         
            var color = d3.scale.category20b();
            var viewToggle = false;
         
            var treemap = d3.layout.treemap()
                .size([width, height])
                .padding(5)
                .value(function(d) { return d[yValue] });
         
            var svg = target.append("svg")
                //.style("position", "relative")
                .attr("width", width + margin.left + margin.right)
                .attr("height", height + margin.top + margin.bottom)
                .attr("x", margin.left)
                .attr("y", margin.top);
         
            var node = svg.selectAll(".node")
                .data(treemap.nodes(data))
                .enter()
                .append("g");
                  
            node.append("rect")
                .attr("class", "node")
                .attr("x", function(d) { return d.x + margin.left })
                .attr("y", function(d) { return d.y + margin.top + 20 })
                .attr("width", function(d) { return d.dx - 0.75 })
                .attr("height", function(d) { return d.dy - 0.75 })
                .attr("fill", function(d) { return color(d[yValue]) })
                .attr("opacity", 0.8)
                .on("mouseover", mouseover)
                .on("mouseout", mouseout)
                .on("click", mouseclick);
                 
            //int values here used for alignment
            node.append("text")
                .attr("class", "treemap-text")
                .attr("font-size", function(d) {return textScale(d[yValue]) })
                .attr("x", function(d) { return +d.x + 5 + margin.left })
                .attr("y", function(d) { return +d.y + (d.dy/2 + 3) + margin.top + 20 })
                .text(function(d) { return d.children ? null : d[xValue] });
             
            function mouseover(d)
            {            
                d3.select(this)
                    .transition()
                    .duration(100)
                    .attr("opacity", 1);
                    
                d3.selectAll("[class=treemap-header-text").remove();
                
                console.log(d);
                svg.append("g")
                    .append("text")
                    .attr("x", margin.left)
                    .attr("y", margin.top)
                    .attr("class", "treemap-header-text")
                    .attr("fill", "#483D8B")
                    .text(d.children? "Total: " + d.value : d[xValue] + ": "  + d[yValue]);
                    
            };
             
            function mouseout(d)
            {
                if (!viewToggle)
                {
                    d3.selectAll("rect")
                        .transition()
                        .duration(100)
                        .attr("opacity", 0.8);
                }
            };
             
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
            };
        }
    }
}

