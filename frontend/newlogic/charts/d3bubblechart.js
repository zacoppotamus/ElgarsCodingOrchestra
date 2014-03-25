function fScatter(xVal, yVal, data, maxRadius)
{
    var width = 1500,
        height = 800,
        radiusMult = 5;
    
    var color = d3.scale.category20b();
    
    var maxElement = d3.max(d3.values(data),function(i){
            return i[yVal];
        });
    
    var svg = d3.select("body").append("svg")
        .attr("class", "mainsvg");
    
    var div = d3.select("body").append("div")
        .attr("class", "hidden")
        .attr("id", "tooltip");
        
    div.append("p")
        .attr("id", xVal);
    div.append("p")
        .attr("id", yVal);
    
    //TODO change charge to be dependant on radius
    var force = d3.layout.force()
        .nodes(data)
        .size([width/1.5, height/1.5])
        .gravity(0.5)
        .charge(-maxElement*100)
        .start();
    
    var nodes = svg.selectAll(".node")
        .data(force.nodes())
        .enter()
        .append("g")
        .attr("class", "node")
        .on("mouseover", mouseover)
        .on("mouseout", mouseout)
        .call(force.drag);
        
    nodes.append("circle")
        .attr("r", function(data)
        {
            return data[yVal] * radiusMult;
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
        var yPos = d.y - ((maxElement-d[yVal])+maxElement)*radiusMult*1.5;
        //Update the tooltip position and value
        d3.select("#tooltip")
          .style("left", xPos + "px")
          .style("top", yPos + "px")
          .select("#"+xVal)
          .text(d[xVal]);
        d3.select("#tooltip")
          .style("left", xPos + "px")
          .style("top", yPos + "px")
          .select("#"+yVal)
          .text(d[yVal]);
        d3.select("#tooltip").classed("hidden", false);
        
        d3.select(this)
            .select("circle")
            .transition()
            .duration(150)
            .attr("r", function(data)
            {
                return data[yVal] * 1.2 * radiusMult;
            });
    };
    
    function mouseout()
    {
        d3.select("tooltip").classed("hidden", true);
        
        d3.select(this)
            .select("circle")
            .transition()
            .duration(150)
            .attr("r", function(data)
            {
                return data[yVal] * radiusMult;
            });
    };
}
