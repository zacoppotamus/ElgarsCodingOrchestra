var jsonYB = [
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
var svgContainer = d3.select("#Something")
        .append("svg")
        .attr("width", 2000)
        .attr("height", 2000);    
var nodes = svgContainer.selectAll("circle")
    .data(jsonYB)
    .enter()
    .append("circle")
var circleAttributes = nodes
    .attr("r", function(d) {return (d.Budget/100);})
    .attr("cx", function(d){return (d.Year - 1995) * 50;})
    .attr("cy", function(d){return (d.Year - 1995) * 50;})
    .style("stroke", "gray")
    .style("fill", "aquamarine")
    .on("mouseover", function(){d3.select(this).style("fill", "aqua");})
    .on("mouseout", function(){d3.select(this).style("fill", "aquamarine");});



