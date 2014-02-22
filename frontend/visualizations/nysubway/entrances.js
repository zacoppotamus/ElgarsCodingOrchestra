// TO DO: Add labels on click
var map = new google.maps.Map(d3.select("#map").node(),
{
  zoom: 12,
  center: new google.maps.LatLng(40.7581769, -73.9469835),
  mapTypeId: google.maps.MapTypeId.TERRAIN
});

// Style the map
var styles =
  [{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#165c64"},{"saturation":34},{"lightness":-69},{"visibility":"on"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"hue":"#b7caaa"},{"saturation":-14},{"lightness":-18},{"visibility":"on"}]},{"featureType":"landscape.man_made","elementType":"all","stylers":[{"hue":"#cbdac1"},{"saturation":-6},{"lightness":-9},{"visibility":"on"}]},{"featureType":"road","elementType":"geometry","stylers":[{"hue":"#8d9b83"},{"saturation":-89},{"lightness":-12},{"visibility":"on"}]},{"featureType":"road.highway","elementType":"geometry","stylers":[{"hue":"#d4dad0"},{"saturation":-88},{"lightness":54},{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"hue":"#bdc5b6"},{"saturation":-89},{"lightness":-3},{"visibility":"simplified"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"hue":"#bdc5b6"},{"saturation":-89},{"lightness":-26},{"visibility":"on"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"hue":"#c17118"},{"saturation":61},{"lightness":-45},{"visibility":"on"}]},{"featureType":"poi.park","elementType":"all","stylers":[{"hue":"#8ba975"},{"saturation":-46},{"lightness":-28},{"visibility":"on"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"hue":"#a43218"},{"saturation":74},{"lightness":-51},{"visibility":"simplified"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":0},{"lightness":100},{"visibility":"simplified"}]},{"featureType":"administrative.neighborhood","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":0},{"lightness":100},{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels","stylers":[{"hue":"#ffffff"},{"saturation":0},{"lightness":100},{"visibility":"off"}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"hue":"#ffffff"},{"saturation":0},{"lightness":100},{"visibility":"off"}]},{"featureType":"administrative","elementType":"all","stylers":[{"hue":"#3a3935"},{"saturation":5},{"lightness":-57},{"visibility":"off"}]},{"featureType":"poi.medical","elementType":"geometry","stylers":[{"hue":"#cba923"},{"saturation":50},{"lightness":-46},{"visibility":"on"}]}];

map.setOptions({styles: styles});

dataURL = encodeURI("http://api.spe.sneeza.me/datasets/nysubway/select?fields=[%22latitude%22,%22longitude%22]");

// Load the JSON data for the entrances
d3.json(dataURL, function(json)
{
	// Create overlay
	var overlay = new google.maps.OverlayView();

	// Add the container when the overlay is added
	// to the map
	overlay.onAdd = function()
	{
		var layer = d3.select(this.getPanes().overlayMouseTarget).append("div")
			.attr("class", "stations");

		// Draw each marker as a separate SVG element
		overlay.draw = function()
		{
			var projection = this.getProjection(),
				padding = 10;

			var marker = layer.selectAll("svg")
					.data(json.data.results)
					.each(transform) //update existing markers
				.enter().append("svg:svg")
					.each(transform)
					.attr("class", "marker");

			// Add a circle
			marker.append("svg:circle")
				.attr("r", 4.5)
				.attr("cx", padding)
				.attr("cy", padding);

			// Add a label
			marker.data(json.data.results)
			.on('mouseover', function(d)
			{
				d3.select(this).append("svg:text")
				.attr("class", "tooltip")
				.attr("x", padding+7)
				.attr("y", padding)
				.attr("dy", ".31em")
				.text(function(d) { return d.name });
			})
			.on('mouseout', function(d)
			{
				d3.selectAll("text").remove();
			});

			function transform(d)
			{
				d = new google.maps.LatLng(+d.latitude, +d.longitude)
				d = projection.fromLatLngToDivPixel(d);
				return d3.select(this)
					.style("left", (d.x - padding) + "px")
					.style("top", (d.y - padding) + "px");
			}

		};
	};

	// Bind overlay to map
	overlay.setMap(map);
});