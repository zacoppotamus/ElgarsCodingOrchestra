eco.charts.vegabarchart = function() {

	return {
		title: "Bar Chart",

		options : {
			width : 800,
			height : 600,
			margin : {
				top: 10,
				right: 10,
				bottom: 20,
				left: 80
			}
		},

		spec : function(data, xValue, yValue){
			console.log('--------\n' + xValue + yValue)

			return {
				"width": this.options.width,
				"height": this.options.height,
				"padding": {
					"top": this.options.margin.top,
					"left": this.options.margin.left,
					"bottom": this.options.margin.bottom,
					"right": this.options.margin.right
				},
				"data": [
					{
						"name": "table",
						"values": data
					}
				],
				"scales": [
					{"name":"x", "type":"ordinal", "range":"width", "domain":{"data":"table", "field":"data."+xValue}},
					{"name":"y", "range":"height", "nice":true, "domain":{"data":"table", "field":"data."+yValue}}
				],
				"axes": [
					{"type":"x", "scale":"x"},
					{"type":"y", "scale":"y"}
				],
				"marks": [
					{
						"type": "rect",
						"from": {"data":"table"},
						"properties": {
							"enter": {
							"x": {"scale":"x", "field":"data."+xValue},
							"width": {"scale":"x", "band":true, "offset":-1},
							"y": {"scale":"y", "field":"data."+yValue},
							"y2": {"scale":"y", "value":0}
							},
							"update": { "fill": {"value":"steelblue"} },
							"hover": { "fill": {"value":"red"} }
						}
					}
				]
			}
		}
	}
};