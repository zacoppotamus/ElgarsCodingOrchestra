'use strict'
 
var target = d3.select("body");
var data = [
    {"state":"Mississippi","value":14},
    {"state":"Oklahoma","value":18},
    {"state":"Delaware","value":4},
    {"state":"Minnesota","value":22},
    {"state":"Illinois","value":174},
    {"state":"Arkansas","value":8},
    {"state":"New Mexico","value":103},
    {"state":"Indiana","value":19},
    {"state":"Louisiana","value":145},
    {"state":"Texas","value":143},
    {"state":"Wisconsin","value":13},
    {"state":"Kansas","value":7},
    {"state":"Connecticut","value":54},
    {"state":"California","value":1816},
    {"state":"West Virginia","value":11},
    {"state":"Georgia","value":88},
    {"state":"North Dakota","value":1},
    {"state":"Pennsylvania","value":119},
    {"state":"Alaska","value":16},
    {"state":"Missouri","value":20},
    {"state":"South Dakota","value":10},
    {"state":"Colorado","value":44},
    {"state":"New Jersey","value":197},
    {"state":"Washington","value":81},
    {"state":"New York","value":748},
    {"state":"Nevada","value":130},
    {"state":"Maryland","value":61},
    {"state":"Idaho","value":8},
    {"state":"Wyoming","value":16},
    {"state":"Arizona","value":123},
    {"state":"Iowa","value":12},
    {"state":"Michigan","value":64},
    {"state":"Utah","value":88},
    {"state":"Virginia","value":70},
    {"state":"Oregon","value":62},
    {"state":"Montana","value":22},
    {"state":"New Hampshire","value":9},
    {"state":"Massachusetts","value":106},
    {"state":"South Carolina","value":30},
    {"state":"Vermont","value":10},
    {"state":"Florida","value":163},
    {"state":"Hawaii","value":62},
    {"state":"Kentucky","value":16},
    {"state":"Rhode Island","value":22},
    {"state":"Nebraska","value":11},
    {"state":"Ohio","value":35},
    {"state":"Alabama","value":20},
    {"state":"North Carolina","value":81},
    {"state":"Tennessee","value":43},
    {"state":"Maine","value":15}
    ]
 
treemap(data, "state", "value", target);
 
function treemap(data, xValue, yValue, target) 
{
    var data = {name: xValue,
        "children": data};
            
    var margin = 
        {
            top: 50, 
            right: 50, 
            bottom: 50, 
            left: 50
        }
    var width = 1400 - margin.left - margin.right,
        height = 700 - margin.top - margin.bottom;
        
    var minSize = 10;
    
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
        .attr("x", function(d) { return +d.x + (d.dx/2 - (d[xValue].length*6)) + margin.left })
        .attr("y", function(d) { return +d.y + (d.dy/2 + 3) + margin.top + 20 })
        .text(function(d) { return d.children ? null : d[xValue] });
     
    function mouseover(d)
    {            
        d3.select(this)
            .transition()
            .duration(100)
            .attr("opacity", 1);
             
        d3.selectAll("[class=treemap-header-text").remove();
                 
        svg.append("g")
            .append("text")
            .attr("x", margin.left)
            .attr("y", margin.top)
            .attr("class", "treemap-header-text")
            .attr("fill", "#483D8B")
            .text((d.children? "Total: " : d[xValue] + ": " ) + d[yValue]);
             
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
