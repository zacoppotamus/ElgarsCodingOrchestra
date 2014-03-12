var nodes = [
  {
    "Year":1997,
    "Budget":1400
  },
  {
    "Year":1998,
    "Budget":1234
  },
  {
    "Year":1999,
    "Budget":3221
  },
  {
    "Year":2000,
    "Budget":4123
  },
  {
    "Year":2001,
    "Budget":1254
  },
  {
    "Year":2002,
    "Budget":9457
  },
  {
    "Year":2003,
    "Budget":1637
  },
  {
    "Year":2004,
    "Budget":1739
  },
  {
    "Year":2005,
    "Budget":8372
  },
  {
    "Year":2006,
    "Budget":2347
  },
  {
    "Year":2007,
    "Budget":5628
  },
  {
    "Year":2008,
    "Budget":2130
  },
  {
    "Year":2009,
    "Budget":2134
  },
  {
    "Year":2010,
    "Budget":7827
  },
  {
    "Year":2011,
    "Budget":1818
  },
  {
    "Year":2012,
    "Budget":2324
  },
  {
    "Year":2013,
    "Budget":1246
  },
  {
    "Year":2014,
    "Budget":7362
  }
]
var width = 1000,
    height = 1000;
    
var force = d3.layout.force()
    .nodes(nodes)
    .size([width,height])
    .on("tick",tick)
    .charge(-1000)
    .start();
    
var svg = d3.select("#container").append("svg")
    .attr("width",width)
    .attr("height",height)

var node = svg.selectAll("g")
    .data(force.nodes())
    .enter().append("g")
    .call(force.drag)
    
var circle = node.append("circle")
    .style("fill","aqua")
    .style("stroke", "gray")
    .attr("r", function(d){return d.Budget/100})

var text = node.append("text")
    .text(function(d) {return d.Year;})
    .attr("text-anchor","middle")
    
function tick(){
    node.attr("transform", function(d){return "translate(" + d.x + ","+ d.y +")"})
    circle.on("mouseover", function(){d3.select(this).style("fill", "aquamarine");})
    circle.on("mouseout", function(){d3.select(this).style("fill", "aqua");});
}
