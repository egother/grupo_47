function plotear(){       

$(document).ready(function() {
	
	//fechas
	var f1=document.getElementById("from").value;
	var f2=document.getElementById("to").value;

	if ( f1.length != 0 && f2.length != 0) {
    //carga el subtitulo
    var subtitulo="Entre las fechas ";
		subtitulo+=f1;
		subtitulo+=" y ";
		subtitulo+=f2;
		
		

    var options = {
        chart: {
            renderTo: 'container',
            type: 'bar',
            cursor: 'pointer'
        },
		title: { text: 'Informe kilo por fecha'},
		subtitle: {	text: subtitulo	},
        series: [{}],
		navigation: {
						buttonOptions: {
							enabled: true
							
					}
					
				},
				exporting: {
						enabled: true
					},
					credits: {
						enabled: false
					}
		
    
    
    };
    

    
    
    var url= "../app/informe.php?informe=barra&f1=";
    url+=f1;
    url+="&f2=";
    url+=f2;
    
    

    $.getJSON(url, function(data) {
        options.series[0].data = data;
        var chart = new Highcharts.Chart(options);
    });

}

});




}
