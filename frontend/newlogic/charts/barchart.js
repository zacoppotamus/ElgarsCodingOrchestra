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

		spec : function(){
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
						"values": [
							{"x":"A", "y":28}, {"x":"B", "y":55}, {"x":"C", "y":43},
							{"x":"D", "y":91}, {"x":"E", "y":81}, {"x":"F", "y":53},
							{"x":"G", "y":19}, {"x":"H", "y":87}, {"x":"I", "y":52}
						]
					}
				],
				"scales": [
					{"name":"x", "type":"ordinal", "range":"width", "domain":{"data":"table", "field":"data.x"}},
					{"name":"y", "range":"height", "nice":true, "domain":{"data":"table", "field":"data.y"}}
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
							"x": {"scale":"x", "field":"data.x"},
							"width": {"scale":"x", "band":true, "offset":-1},
							"y": {"scale":"y", "field":"data.y"},
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