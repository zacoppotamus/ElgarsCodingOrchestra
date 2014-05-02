'use strict';

/* directives */

angular.module('eco.directives', [])
.directive('ecochart', function(dataService) {

    // Inherit scope from parent controller
    return {
        restrict: 'AEC',
        terminal: true,
        scope: {
            type: '=',
            currentData: '='
        },
        link: function(scope, element, attrs) {
            var data = dataService.getCurrentData();
            var target = d3.select(element[0]);

            // each number corresponds to a different type of visualization
            var chartType = dataService.getSelectedVizType();
            var vizOptions = dataService.vizOptions;

            if (chartType == 0) {
                var values = vizOptions[chartType].options['values'];
                var names = vizOptions[chartType].options['names'];

                eco.charts.d3piechart().render(data, names, values, target);
            }
            else if (chartType == 1) {
                var xValue = vizOptions[chartType].options['xAxis'];
                var yValue = vizOptions[chartType].options['yAxis'];

                eco.charts.d3barchart().render(data, xValue, yValue, target);
            }
            else if (chartType == 2) {
                var xValue = vizOptions[chartType].options['x'];
                var yValue = vizOptions[chartType].options['y'];
                var maxRadius = vizOptions[chartType].options['maxRadius'];

                eco.charts.d3bubblechart().render(data, xValue, yValue, maxRadius, target);
            }
            else if (chartType == 3) {
                var xValue = vizOptions[chartType].options['name'];
                var yValue = vizOptions[chartType].options['size'];

                eco.charts.d3treemap().render(data, xValue, yValue, target);
            }
            else if (chartType == 4) {
                var latitude = vizOptions[chartType].options['latitude'];
                var longitude = vizOptions[chartType].options['longitude'];

                eco.charts.d3map().render(data, latitude, longitude, target);
            }
            else if (chartType == 5) {
                var xValue = vizOptions[chartType].options['x'];
                var yValue = vizOptions[chartType].options['y'];
                var zValue = vizOptions[chartType].options['z'];

                eco.charts.glscatter().render(data, xValue, yValue, zValue, element[0]);
            }
            else if (chartType == 6) {
                var xValue = vizOptions[chartType].options['xValue'];
                var yValue = vizOptions[chartType].options['yValue'];

                eco.charts.d3linegraph().render(data, xValue, yValue, target);
            }           
        }
    }
});