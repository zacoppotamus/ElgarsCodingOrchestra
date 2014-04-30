d3.csv("crimes.csv", function(error, csv)
{
    var years;
    var x = "Type";
    var elements = csv[0];
    console.log(x);
 
    var pie = d3.layout.pie();

    var w = 400;
    var h = 400;
    var color = d3.scale.category10();
    var outerRadius = w / 2;
    var innerRadius = w / 6;
    var arc = d3.svg.arc()
        .innerRadius(innerRadius)
        .outerRadius(outerRadius);
                    
    var svg = d3.select("body")
        .append("svg")
        .attr("width", w)
        .attr("height", h);

    var arcs = svg.selectAll("g.arc")
        .data(pie(csv))
        .enter()
        .append("g")
        .attr("class", "arc")
        .attr("transform", "translate(" + outerRadius + ", " + outerRadius + ")");

    arcs.append("path")
        .attr("fill", function(d, i) 
        {
            return color(i);
        })
        .attr("d", arc);
        

    arcs.append("text")
        .attr("transform", function(d) 
        {
            return "translate(" + arc.centroid(d) + ")";
        })
        .attr("text-anchor", "middle")
        .text(function(d) 
        {
            return d.value;
        });

});


