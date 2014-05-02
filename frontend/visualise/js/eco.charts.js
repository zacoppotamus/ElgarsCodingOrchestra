// anonymous function to make the eco charts object
// available throughout the app.
(function() {

    var eco = window.eco || (window.eco = {});

    eco.charts = {};

})();

eco.charts = function() {
    return [
        {
            'id' : 0, 
            'name' : 'Pie Chart', 
            'choices' : ['names', 'values'],
            'options' : {
                'names' : null,
                'values' : null
            },
            'description' : 'A pie chart is a circular chart divided into sectors, illustrating numerical proportion. In a pie chart, the arc length of each sector (and consequently its central angle and area), is proportional to the quantity it represents.',
            'image' : 'img/piechart.png'
        },
        {
            'id': 1,
            'name':'Bar Chart',
            'choices':['xAxis', 'yAxis'],
            'options' : {
                'xAxis' : null,
                'yAxis' : null
            },
            'description' : 'A bar chart or bar graph is a chart with rectangular bars with lengths proportional to the values that they represent. The bars can be plotted vertically or horizontally. A vertical bar chart is sometimes called a column bar chart.',
            'image' : 'img/barchart.png'
        },
        {
            'id': 2,
            'name':'Bubble Chart',
            'choices':['x', 'y'],
            'options' : {
                'x' : null,
                'y' : null
            },
            'description' : 'A bubble chart with a forced layout.',
            'image' : 'img/bubblechart.png'
        },
        {
            'id': 3,
            'name': 'Treemap',
            'choices': ['name', 'size'],
            'options': {
                'name' : null,
                'size' : null
            },
            'description' : 'A space filling visualization of data hierarchies and proportion between elements. The different hierarchical levels create visual clusters through the subdivision into rectangles proportionally to each element\'s value. Treemaps are useful for representing the different proportion of nested hierarchical data structures.',
            'image' : 'img/treemap.png'
        },
        {
            'id': 4,
            'name':'Map',
            'choices':['latitude', 'longitude'],
            'options' : {
                'latitude' : null,
                'longitude' : null
            },
            'description' : 'Plot a number of coordinates on a Google Map overlay.',
            'image' : 'img/map.png'
        },
        {
            'id': 5,
            'name':'3D Scatter Plot',
            'choices':['x', 'y', 'z'],
            'options' : {
                'x' : null,
                'y' : null,
                'z' : null
            },
            'description' : '3D Scatter plot. Visualise your data in 3 different axes, with each field representing numerical values.',
            'image' : 'img/glscatter.png'
        },
        {
            'id': 6,
            'name':'Line Graph',
            'choices':['xValue', 'yValue'],
            'options' : {
                'xValue' : null,
                'yValue' : null
            },
            'description' : 'Stream Graph: For continuous data such as time series, a streamgraph can be used in place of stacked bars.',
            'image' : 'img/linegraph.png'
        }
    ];
}