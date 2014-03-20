var width = 400,
    height = 400,
    xPad = width/10,
    yPad = height/10;

var svg = d3.select("body").append("svg")
    .attr("width", width + xPad)
    .attr("height", height + yPad);
    
d3.json("crimetype.json", function(error, json)
{
    var jsonResults = json.data.results;
    
    var rootNodes = jsonResults;
    
    var force = d3.layout.force()
        .nodes(rootNodes)
        .size([width, height])
        .gravity(.2)
        .charge(-200)
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
        .attr("r", 11.5);
        
    nodes.append("text")
        .attr("x", function(d) { return -3 * d.Type.length})
        .attr("y", -15)
        .text(function(d) { return d.Type});
       
        
    force.on("tick", function() 
    {
        nodes.attr("transform", function(d) 
        { 
            return "translate(" + d.x + "," + d.y + ")"; 
        });
    });
    
    function mouseover() 
    {
        d3.select(this)
            .select("circle")
            .transition()
            .duration(150)
            .attr("r", 16);
        d3.select(this)
            .select("text")
            .transition()
            .duration(150)
            .attr("y", -20);
    }

    function mouseout() 
    {
        d3.select(this)
            .select("circle")
            .transition()
            .duration(200)
            .attr("r", 11.5);
        d3.select(this)
            .select("text")
            .transition()
            .duration(200)
            .attr("y", -15);
    }
        
});
