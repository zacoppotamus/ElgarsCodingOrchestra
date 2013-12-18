var margin = {top:19.5, right:19.5, bottom:19.5, left:39.5},
	width = 960 - margin.right,
	height = 500 - margin.top - margin.bottom;

// All drawing is here
d3.text("data/MajorFelonies.csv", function(datasetText)
{
	// Draw initial points
	window.year = 2000;
	addLegend();
	drawInitial(window.year);
	updateDots(window.year);

	// Add overlay for year label
	var box = label.node().getBBox();

	var overlay = svg.append("rect")
		.attr("class", "overlay")
		.attr("x", box.x)
		.attr("y", box.y)
		.attr("width", box.width)
		.attr("height", box.height)
		.on("mouseover", enableInteraction);

	function addLegend()
	{
		d3.csv("data/MajorFelonies.csv", function(data)
		{
			var legend_dots = svg.append("g")
					.attr("class", "legend_dots")
				.selectAll(".legend_type")
					.data(data)
				.enter()
					.append("circle")
						.attr("class", "l_dot")
						.attr("fill", function(d, i)
						{
							return colorScale(i);
						})
						.attr("r", "7px")
						.attr("cx", width-200)
						.attr("cy", function(d,i)
						{
							return legendScale(i);
						});

			var legend_text = svg.append("g")
					.attr("class", "legend_text")
				.selectAll(".legend_type")
					.data(data)
				.enter()
					.append("text")
						.attr("class", "l_text")
						.attr("text-anchor", "start")
						.attr("x", width-185)
						.attr("y", function(d,i)
				{
					return legendScale(i)+6;
				})
				.text(function(d,i)
				{
					return d.Type;
				});
		});


	}

	function drawInitial(year)
	{
		window.data = d3.csv.parseRows(datasetText);
		currentData = [];
		for (var i=1; i<8; i++)
		{
			currentData.push(window.data[i][window.year-1999]);
		}

		//Add initial dots
		var dot = svg.append("g")
			.attr("class", "dots")
		.selectAll(".dot")
			.data(currentData)
		.enter().append("circle")
			.attr("class", "dot")
			.attr("fill", function(d, i)
			{
				return colorScale(i);
			});
	}

	function updateDots(year)
	{
		window.data = d3.csv.parseRows(datasetText);
		currentData = [];
		for (var j=1; j<8; j++)
		{
			currentData.push(window.data[j][window.year-1999]);
		}

		d3.selectAll(".dot")
			.data(currentData)
			.transition()
			.duration(75)
			.attr("cy", function(d)
			{
				return yScale(d);
			})
			.attr("cx", function()
			{
				return xScale(window.year-1999);
			})
			.attr("r", function(d)
			{
				return 12;
			});
	}

	function enableInteraction()
	{
		var yearScale = d3.scale.linear()
			.domain([2000, 2011])
			.range([box.x + 10, box.x + box.width - 10])
			.clamp(true);

		overlay
			.on("mouseover", mouseover)
			.on("mouseout", mouseout)
			.on("mousemove", mousemove)
			.on("touchmove", mousemove);

		function mouseover()
		{
	      label.classed("active", true);
	    }

	    function mouseout()
	    {
	      label.classed("active", false);
	    }

	    function mousemove()
	    {
			displayYear(yearScale.invert(d3.mouse(this)[0]));
			window.year = getYear(yearScale.invert(d3.mouse(this)[0]));
	    }
	}

	function displayYear(year)
	{
		label.text(Math.round(year));
		updateDots(window.year);
	}

	function getYear(year)
	{
		return Math.round(year)
	}
})

//Replace with max/min of dataset
var yScale = d3.scale.linear().domain([0, 50000]).range([height-margin.top-margin.bottom, 0]);
var xScale = d3.scale.linear().domain([1, 12]).range([150, 650]);
var rScale = d3.scale.linear().domain([0, 50000]).range([7, 27]);
var legendScale = d3.scale.linear().domain([0,11]).range([margin.top+15, height-80]);
var colorScale = d3.scale.category10();

//var yScale = d3.scale.linear().domain([0, 50000]).range([margin.top, height]);
var yAxis = d3.svg.axis().scale(yScale).orient("left");

var svg = d3.select("#chart").append("svg")
	.attr("width", width + margin.left + margin.right)
	.attr("height", height + margin.top + margin.bottom)
	.append("g")
		.attr("transform", "translate(" + margin.left + "," + margin.top + ")");

// Add y-axis
svg.append("g")
	.attr("class", "y axis")
	.attr("transform", "translate(" + margin.left + ",0)")
	.call(yAxis);

// Add y-axis label
svg.append("text")
	.attr("class", "y label")
	.attr("text-anchor", "end")
	.attr("y", 45)
	.attr("dy", ".75em")
	.attr("transform", "rotate(-90)")
	.text("number of crimes commited")

// Add year label
var label = svg.append("text")
	.attr("class", "year label")
	.attr("y", height-50)
	.attr("x", width-200)
	.text("Year");