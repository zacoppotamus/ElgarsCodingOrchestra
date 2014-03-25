eco.charts.gpiechart = function()
{
    return {
        title: 'Google Chart',
        options: {
            width: 800,
            height: 600
        },
        render: function(data, xValue, yValue, target)
        {
            google.load('visualization', '1', {'packages':['corechart']});

            google.setOnLoadCallback(drawChart);

            function drawChart()
            {
                var data = new google.visualization.DataTable();
                data.addColumn('string', xValue);
                data.addColumn('number', yValue);

                for(i=0; i<data.length; i++)
                {
                    data.addRow([data[i][xValue], data[i][yValue]]);
                }

                var chartOptions = {
                    width: this.options.width,
                    height: this.options.height,
                    title: this.title
                };

                var chart = new google.visualization.PieChart(target);
                chart.draw(data, chartOptions);

            }

            return this;
        }

    }
};

