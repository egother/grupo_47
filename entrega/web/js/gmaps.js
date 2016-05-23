		function initialize() {
		  var lat= $( "#latitud" ).val();
		  var lon= $( "#longitud" ).val();
		  
		  
		  
		  var mapOptions = {
			zoom: 16,
			center: new google.maps.LatLng(lat, lon),
			panControl: false,
			zoomControl: false,
			scaleControl: false
		  };

		  var map = new google.maps.Map(document.getElementById('map-canvas'),
			  mapOptions);
			  
		var marker = new google.maps.Marker({
			  position: new google.maps.LatLng(lat, lon),
			  map: map,
			  title: 'Banco Alimentario'
		});
		}

		function loadScript() {
		  var script = document.createElement('script');
		  script.type = 'text/javascript';
		  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
			  'callback=initialize';
		  document.body.appendChild(script);
		}

		
