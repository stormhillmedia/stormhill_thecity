// Google Maps Initialize
// Init Google Map
/*function initialize() {
	var mapCanvas = document.getElementById('map-canvas');
	var mapOptions = {
		center: mylatlng,
		zoom: 16,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	var map = new google.maps.Map(mapCanvas, mapOptions);
	
	var lat = document.getElementById("map-coords-lat").value;
	var lng = document.getElementById("map-coords-lng").value;
	var mylatlng = new google.maps.LatLng(lat,lng);
	var marker = new google.maps.Marker({
	  position: mylatlng,
	  map: map,
	  //title: 'This is the Place'
	});

}
google.maps.event.addDomListener(window, 'load', initialize);
*/

/*
http://www.advancedcustomfields.com/resources/google-map/
*/
(function($) {

/*
*  render_map
*
*  This function will render a Google Map onto the selected jQuery element
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$el (jQuery element)
*  @return	n/a
*/

function render_map( $el ) {

	// var
	var markerbox = $('.groupslist');
	var $markers = markerbox.find('.marker');
	// vars
	var args = {
		zoom		: 10,
		center		: new google.maps.LatLng(30.22962584, -97.83730208), // set to location of the church
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};

	// create map	        	
	var map = new google.maps.Map( $el[0], args);

	// add a markers reference
	map.markers = [];

	// add markers
	$markers.each(function(){
    	add_marker( $(this), map );
	});
	// center map
	center_map( map );
}

/*
*  add_marker
*
*  This function will add a marker to the selected Google Map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	$marker (jQuery element)
*  @param	map (Google Map object)
*  @return	n/a
*/

function add_marker( $marker, map ) {
	// var
	var latlng = new google.maps.LatLng( $marker.attr('data-lat'), $marker.attr('data-lng') );
	// get the title info
	var mytitle = $marker.children('.output').find('.mled').html();
	//var prokid = $marker.find('.kids').html();
	var zone = $marker.find('.zone').html();
	//console.log(prokid);
	// fun with symbols
	var image;
	// family zone
	var fam_img = '/wp-content/plugins/stormhill_thecity/images/home-3.png';
	// yopro zone
	var yopro_img = '/wp-content/plugins/stormhill_thecity/images/yopro-3.png';
	// college zone
	var college_img = '/wp-content/plugins/stormhill_thecity/images/college-3.png';
	// youth zone
	var youth_img = '/wp-content/plugins/stormhill_thecity/images/youth-3.png';
	//var kidimg = '/wp-content/plugins/stormhill_thecity/images/kids.png';
	if (zone === 'Family'){
		image = fam_img;
	}
	if (zone === 'Young Professionals'){
		image = yopro_img;
	}
	if (zone === 'College'){
		image = college_img;
	}
	if (zone === 'Youth'){
		image = youth_img;
	}

	// create marker
	var marker = new google.maps.Marker({
		position	: latlng,
		map			: map,
		title: mytitle,
		icon: image
	});
	// add to array
	map.markers.push( marker );
	// if marker contains HTML, add it to an infoWindow
	if( $marker.html() )
	{
		// create info window
		var infowindow = new google.maps.InfoWindow({
			content		: $marker.find('.output').html()
		});
		// show info window when marker is clicked
		google.maps.event.addListener(marker, 'click', function() {
			infowindow.open( map, marker );
		});
	}
}

/*
*  center_map
*
*  This function will center the map, showing all markers attached to this map
*
*  @type	function
*  @date	8/11/2013
*  @since	4.3.0
*
*  @param	map (Google Map object)
*  @return	n/a
*/

function center_map( map ) {
	// vars
	var bounds = new google.maps.LatLngBounds();
	// loop through all markers and create bounds
	$.each( map.markers, function( i, marker ){
		var latlng = new google.maps.LatLng( marker.position.lat(), marker.position.lng() );
		bounds.extend( latlng );
	});
	// only 1 marker?
	if( map.markers.length == 1 )
	{
		// set center of map
	    map.setCenter( bounds.getCenter() );
	    map.setZoom( 10 );
	}
	else
	{
		// fit to bounds
		map.fitBounds( bounds );
	}
}

/*
*  document ready
*
*  This function will render each map when the document is ready (page has loaded)
*
*  @type	function
*  @date	8/11/2013
*  @since	5.0.0
*
*  @param	n/a
*  @return	n/a
*/

$(document).ready(function(){
	$('.hf-g-map').each(function(){
		render_map( $(this) );
	});
});
})(jQuery);

// Load map of church location only

function render_church_map( $el ) {
	// vars
	var myLatLng = {lat: 30.22962584, lng: -97.83730208}; // location of the church
	var churchname = 'Hope in the City';
	var church_img = '/wp-content/plugins/stormhill_thecity/images/hitc.png';
	var args = {
		zoom		: 10,
		center		: myLatLng, // set to location of the church
		mapTypeId	: google.maps.MapTypeId.ROADMAP
	};

	// create map	        	
	var map = new google.maps.Map( $el[0], args);
	
	var marker = new google.maps.Marker({
		position	: myLatLng,
		map			: map,
		title: churchname,
		icon: church_img
	});

}

jQuery(document).ready(function($){
	$('.hitc-church-map').each(function(){
		render_church_map( $(this) );
	});
});


/* Events 
-- moves the in-text image to top of event record.. Darn you ontheCity api -- and your lousy way of placing images */
jQuery(document).ready(function($){
	$('.event').each(function(){
		var image = $(this).find('.eve-description img');
		if ($(image).length) {
			var imgbox = $(image);
			imgbox.addClass( "imgbox");
			imgbox.insertAfter($(this).find('.eve-title'));
		}
	});
});
