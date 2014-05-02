eco.charts.d3map = function() {

    return {
        title: 'Map',

        render: function(data, latitude, longitude, target) {

            var map = new google.maps.Map(target.node(),
            {
                zoom: 2,
                center: new google.maps.LatLng(0, 0),
                mapTypeId: google.maps.MapTypeId.TERRAIN
            });

            // Create overlay
            var overlay = new google.maps.OverlayView();

            // Add the container when the overlay is added
            // to the map
            overlay.onAdd = function()
            {
                var layer = d3.select(this.getPanes().overlayMouseTarget).append("div")
                    .attr("class", "map-dot");

                // Draw each marker as a separate SVG element
                overlay.draw = function()
                {
                    var projection = this.getProjection(),
                        padding = 10;

                    var marker = layer.selectAll("svg")
                            .data(data)
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
                    // marker.data(json.data.results)
                    // .on('mouseover', function(d)
                    // {
                    //  d3.select(this).append("svg:text")
                    //  .attr("class", "tooltip")
                    //  .attr("x", padding+7)
                    //  .attr("y", padding)
                    //  .attr("dy", ".31em")
                    //  .text(function(d) { return d.name });
                    // })
                    // .on('mouseout', function(d)
                    // {
                    //  d3.selectAll("text").remove();
                    // });
    
                    function transform(d)
                    {
                        d = new google.maps.LatLng(+d[latitude], +d[longitude])
                        d = projection.fromLatLngToDivPixel(d);
                        return d3.select(this)
                            .style("left", (d.x - padding) + "px")
                            .style("top", (d.y - padding) + "px");
                    }

                };
            };

            // Bind overlay to map
            overlay.setMap(map);

            return this;
        }
    }
}
