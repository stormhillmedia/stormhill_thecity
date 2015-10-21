<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/*****************************/
/*****************************/
/**** list testimonies ****/
/*****************************/
/*****************************/

function shcaa_list_testimonies(){	
	// attempt to get cached request
	$transient_key = "_shcaa_testimonies_feed";
	// If cached (transient) data are used, output an HTML
	// comment indicating such
	$cached = get_transient( $transient_key );
	//if ( false !== $cached ) {
	//	$mytestimonies = $cached;
	//} else {
		
		// create array of the testimonies filtered by the args and include the tags
		$args = array();
		//$args['group_types'] = 'Hope';
		//$args['include_addresses'] = 'true';
		$the_city = new TheCity('hitc');
		$topics = $the_city->topics()->titles();
		
		print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $topics ); print_r('</pre>');
		$mytestimonies = array();
		$i = 0;
		foreach($topics as $indx => $top) {  
			// get the object
			$rawtopic = $topics->select($indx);
			
			// get the body
			$topic = accessProtected($rawtopic, 'data');
			// starting
			$mytestimonies[$i]['starting_at'] = $topic->starting_at;	
			// ending
			$mytestimonies[$i]['ending_at'] = $topic->ending_at;			
			// title
			$mytestimonies[$i]['title'] = $topic->title;
			// topic_items
			$mytestimonies[$i]['topic_items'] = $topic->topic_items;	// array	
			//body
			$mytestimonies[$i]['body'] = $topic->body;			
			// shorturl
			$mytestimonies[$i]['short_url'] = $topic->short_url;
			// Address Items
            
            $mytestimonies[$i]['street'] = $topic->addresses->street;
			$mytestimonies[$i]['street2'] = $topic->addresses->street2;
			$mytestimonies[$i]['city'] = $topic->addresses->city;
			$mytestimonies[$i]['state'] = $topic->addresses->state;
			$mytestimonies[$i]['zipcode'] = $topic->addresses->zipcode;

            $mytestimonies[$i]['location_type'] = $topic->addresses->location_type;
			$mytestimonies[$i]['latitude'] = $topic->addresses->latitude;
            $mytestimonies[$i]['longitude'] = $topic->addresses->longitude;
            	
		$i++;
		} // end foreach
		print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mytestimonies ); print_r('</pre>');
		//set_transient( $transient_key, $mytestimonies, 60*60*12 );
	//} // end if not cached
	
	// testimonies output
	$output = '<ul class="topiclist">';
	$cnt = 0;
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mytestimonies ); print_r('</pre>');
	foreach ($mytestimonies as $myeve){
		
		$top_id = $myeve['eve_id'];
		$parentid = $myeve['parentid'];
		
		$image = $myeve['image'];
		$name = $myeve['name'];
		$description = $myeve['description'];
		$neighborhood = $myeve['neighborhood'];
		$invite = $myeve['invite'];
		$cityurl = $myeve['cityurl'];
		
				
		// Get Tags
		$dtags = $myeve['mytags']; 
		
		if (is_array($dtags) && !empty($dtags)){
			//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $dtags ); print_r('</pre>');
			$mytags = '<div class="eve-tags"';
			foreach ($dtags as $dtag){
				if (is_array($dtag)){
					
					//foreach ($tag as $el){
						$mytags .= '<span> | '.$dtag['name'].' | </span>';
					//} // end inner foreach
					
				} // end if
			} // end foreach
			$mytags .= '</div>';
		} else {
			$mytags = '';
		}

		$output .= '<li class="topic">';
		  // image
		  if (isset($image) && !empty($image) ){
			  $output .= '<div class="eve-image"><img src="'.$image.'" alt="'.$name.'" title="'.$name.'" /></div>';
		  }
		  // name
		  if (isset($name) && !empty($name) ){
			  $output .= '<div class="eve-name">'.$name.'</div>';
		  }
		  // description
		  if (isset($description) && !empty($description) ){
			  $output .= '<div class="eve-description">'.$description.'</div>';
		  }
		  // Invitation
		  if (isset($invite) && !empty($invite) ){
			  $output .= '<div class="eve-invite">'.$invite.'</div>';
		  }
		  
		  // neighborhood
		  if (isset($neighborhood) && !empty($neighborhood) ){
			  $output .= '<div class="eve-neighborhood">'.$neighborhood.'</div>';
		  }
		  
		  // Location
		  $output .= '<h3>Location</h3>';
		  // location name
		  if (isset($add_name) && !empty($add_name) ){
			  $output .= '<div class="eve-add-name">'.$add_name.'</div>';
		  }
		  $output .= '<div class="eve-add">';
			  // location street
			  if (isset($add_street) && !empty($add_street) ){
				  $output .= '<div class="eve-add-street">'.$add_street.', </div>';
			  }
			  // location street 2
			  if (isset($add_street2) && !empty($add_street2) ){
				  $output .= ' <div class="eve-add-street2">'.$add_street2.', </div> ';
			  }
			  // location city
			  if (isset($add_city) && !empty($add_city) ){
				  $output .= ' <div class="eve-add-city">'.$add_city.' </div> ';
			  }
			  // location state
			  if (isset($add_state) && !empty($add_state) ){
				  $output .= ' <div class="eve-add-state">'.$add_state.', </div> ';
			  }
			  // location zip
			  if (isset($add_zipcode) && !empty($add_zipcode) ){
				  $output .= ' <div class="eve-add-zipcode">'.$add_zipcode.' </div> ';
			  }
		  // end address box
		  $output .= '</div>';
		  
		  // testimonies tags
		  if ( isset($mytags) && !empty($mytags)  ){
			  $output .= $mytags;
		  }

		  // location coords
		  if ( (isset($add_longitude) && !empty($add_longitude)) && (isset($add_latitude) && !empty($add_latitude)) ){
			  $output .= '<div class="eve-add-coords">'.$add_longitude.','.$add_latitude .', </div>';
		  }
			  
		  
	  $output .= '</li>';
	}
	$output .= '</ul>';
	
	return $output;
}

// Add Shortcode 
// calls the output function from the plugin
function add_hitc_testimonies($atts){
	$a = shortcode_atts( array(
		'heading' => '',
    ), $atts );
	$output = '';
	$output .= '<div class="testimonies-wrapper">';
	$output .= '<div class="inner">';
	if(isset($a['heading']) && !empty($a['heading']) ){
		$output .= '<h2>'.$a['heading'].'</h2>';
	}
	$output .= shcaa_list_testimonies();
	$output .= '</div></div>';
	return $output;
}
add_shortcode( 'hitc_testimonies_feed', 'add_hitc_testimonies' );



?>