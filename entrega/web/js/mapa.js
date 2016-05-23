function mostrarMapa(){

 /*   var lat            = -34.878110;
    var lon            = -57.895584; */
    
    var lat= $( "#latitud" ).val()
    var lon= $( "#longitud" ).val()
    var zoom = 16;


    var fromProjection = new OpenLayers.Projection("EPSG:4326");   // Transform from WGS 1984
    var toProjection   = new OpenLayers.Projection("EPSG:900913"); // to Spherical Mercator Projection
 
    var position       = new OpenLayers.LonLat(lon,lat).transform( fromProjection, toProjection);
   
   
    map = new OpenLayers.Map("mapa", { controls: [] });
		
		map.addControl(new OpenLayers.Control.PanZoomBar());
		map.addControl(new OpenLayers.Control.LayerSwitcher({}));
		map.addControl(new OpenLayers.Control.OverviewMap());
		
    var mapnik         = new OpenLayers.Layer.OSM();
    map.addLayer(mapnik);
 
    var markers = new OpenLayers.Layer.Markers( "Marcadores" );
   // var icono = new OpenLayers.Icon("bandera.png");
    map.addLayer(markers);
    
    
     markers.addMarker(new OpenLayers.Marker(position));

 
    map.setCenter(position, zoom);

}
