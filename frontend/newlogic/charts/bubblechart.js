eco.charts.bubblechart = function() {

	return {
		title : 'Bubble Chart',
		description : 'blabla',
		model : eco.models.points({

		}),

		options : {
			width : {
				title : 'Width',
				type : 'number',
				value : 800
			},

			height : {
				title : 'Height',
				type : 'number',
				value : 500
			}

		},

		render : function(data, yName, xName, target) {
			var width = options.width.value,
				height = options.height.value,
				color = d3.scale.category20c();

			var bubble = d3.layout.pack()
				.sort(null)
				.size([height, width])
				.padding(1.5);

			var svg = d3.select(target)
				.append("svg")
					.attr("width", width)
					.attr("height", height)
					.attr("class", "bubble-chart");

			d3.json(data, function(error, data) {

			})
		}
	}



}