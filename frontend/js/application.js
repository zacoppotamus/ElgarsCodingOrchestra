function printDatasets()
{
	$(document).ready(function() {
		$.getJSON("//api.spe.sneeza.me/datasets", function(data) {
			$.each(data.data.datasets, function(key,val) {
				// No description field yet.
				var description = '';
				var size = 42;
				var tr = $('<tr><td id="dataset"><a href="dataset.html">'+val+'</a></td><td>'+description+'</td><td>'+size+'</td></tr>');
				$('table').append(tr);
			})
		})
	});
}

function vizPrintDatasets()
{
	$(document).ready(function() {
		$.getJSON("//api.spe.sneeza.me/datasets", function(data) {
			$.each(data.data.datasets, function(key,val) {
				var rb = $('<div class="row"><div class="btn-group" data-toggle="buttons"><label class="btn btn-default btn-block"><input type="radio" name="options" id="option">'+val+'</label></div></div>');
				$('#datasets').append(rb);
			})
		})
		$(document).on('mousedown', '.btn', function() {
			$(this).button();
		})
	});
}

function printDataset()
{
	$(document).ready(function() {
		// Update to GET value
		dataset = window.location.search.slice(1);
        $.getJSON("http://api.spe.sneeza.me/datasets/"+dataset+"select", function(result){
            $.each(result.data.results[0], function(k, header){
                $('.table').append("<th>" + k + "</th>");
            });
            $.each(result.data.results, function(i, row){
                var tr;
                $.each(row, function(j, col){
                    tr = tr + "<td>" + col + "</td>";
                });
                $('.table').append("<tr>" + tr + "</tr>");
	        });
        });
    });
}
