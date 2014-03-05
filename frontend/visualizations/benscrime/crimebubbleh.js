dataURL = encodeURI("http://api.spe.sneeza.me/select?dataset=benscrimedata&fields=");
d3.json(dataURL,function(data)) {
    custom_bubble_chart.init(data);
    custom_bubble_chart.toggle_view('all');
}

$(document).ready(function() {
    $('view_selection a').click(function(){
        var view_type = $(this).attr('id');
        $('#view_selection a').removeClass('active');
        $(this).toggleClass('active');
        custom_bubble_chart.toggle_view(view_type);
        return false;
    });
});