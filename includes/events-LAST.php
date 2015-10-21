<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/*****************************/
/*****************************/
/**** list events ****/
/*****************************/
/*****************************/


function shcaa_list_events($eventtag=''){	
	// attempt to get cached request
	$transient_key = "_shcaa_events_feed";
	// If cached (transient) data are used, output an HTML
	// comment indicating such
	$cached = get_transient( $transient_key );
	if ( false !== $cached ) {
		$myevents = $cached;
	} else {
		
		// create array of the events filtered by the args and include the tags
		$args = array();
		//$args['group_types'] = 'Hope';
		//$args['include_addresses'] = 'true';
		$the_city = new TheCity('hitc');
		$events = $the_city->events(111);
	//  print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 11px; margin: 10px">'); print_r( $events ); print_r('</pre>');
		$event_titles = $events->titles(); 
		
		$myevents = array();
		$i = 0;
		foreach($event_titles as $indx => $eve) {  
			// get the object
			$rawevent = $events->select($indx);
			
			// get the body
			$event = accessProtected($rawevent, 'data');
			// unique id
			$myevents[$i]['eve_id'] = $event->euid;
			// starting
			$myevents[$i]['starting_at'] = $event->starting_at;	
			// ending
			$myevents[$i]['ending_at'] = $event->ending_at;			
			// title
			$myevents[$i]['title'] = $event->title;
			// event_items
			$myevents[$i]['event_items'] = $event->event_items;	// array	
			//body
			$myevents[$i]['body'] = $event->body;			
			// shorturl
			$myevents[$i]['short_url'] = $event->short_url;

            	
		$i++;
		} // end foreach
		//print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 12px;  margin: 10px">'); print_r( $event ); print_r('</pre>');
		set_transient( $transient_key, $myevents, 60*60*1 );
	} // end if not cached
	
	// events output
	if (!isset($myevents) || empty($myevents) ){ return;}
	  $output = '<ul class="eventlist">';
	  $cnt = 0; 
	 // print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 11px; margin: 10px">'); print_r( $myevents ); print_r('</pre>');
	  $dupcheck = array();
	  foreach ($myevents as $myeve){	
		  $eve_id = $myeve['eve_id'];		
		  //$image = $myeve['image'];
		  $title = trim($myeve['title']);

		  // set a virtual event category by detecting substring in the title
		  $tag = 'General';
		  if (strpos($title,'Equipping Class') !== false) {
			  $tag = 'Equipping Class';
		  }
		  if (strpos($title,'Sunday Morning Worship') !== false) {
			  $tag = 'Sunday Morning Worship';
		  }

		  if( !isset($eventtag) || empty($eventtag) || ( isset($eventtag) && $eventtag == $tag )) {
			  if(!in_array($title,$dupcheck)) {  
			  
				  $starting_at = $myeve['starting_at'];
				  $ending_at = $myeve['ending_at'];
				  $event_items = $myeve['event_items']; // array - may contain images??
				  $body = $myeve['body'];
				  $short_url = $myeve['short_url'];
				  
				  // dates
				  if ( isset($starting_at) && !empty($starting_at) ){
					  if( $tag == 'Equipping Class' || $tag == 'Sunday Morning Worship'){
					  	$dates = datetransmog($starting_at,$ending_at,true);
					  } else {
						 $dates = datetransmog($starting_at,$ending_at);
					  }
				  }
				  $output .= '<li class="event">';
					// image
				   // if (isset($image) && !empty($image) ){
				  //	  $output .= '<div class="eve-image"><img src="'.$image.'" alt="'.$title.'" title="'.$title.'" /></div>';
				   // }
					// name
					if (isset($title) && !empty($title) ){
						$output .= '<h3 class="eve-title">'.$title.'</h3>';
					}
					// dates
					if (isset($dates) && !empty($dates) ){
						$output .= '<div class="eve-dates">'.$dates.'</div>';
					}
					// description
					if (isset($body) && !empty($body) ){
						$output .= '<div class="eve-description">'.$body.'</div>';
					}
					// link
					if (isset($short_url) && !empty($short_url) ){
						$output .= '<a href="'.$short_url.'" target="_blank" class="eve-link button">Find out More &raquo;</a>';
					}
	
				$output .= '</li>';
			} // end if dupcheck
	  } // end if in tag
	  $dupcheck[] = $title;
	  } // end foreach
	  $output .= '</ul>';
	
	return $output;
}

// Add Shortcode 
// calls the output function from the plugin
function add_hitc_events($atts){
	$a = shortcode_atts( array(
		'heading' => '',
		'eventtag' => '',
    ), $atts );
	$output = '';
	$output .= '<div class="events-wrapper">';
	$output .= '<div class="inner">';
	if(isset($a['heading']) && !empty($a['heading']) ){
		$output .= '<h2>'.$a['heading'].'</h2>';
	}
	$output .= shcaa_list_events($a['eventtag']);
	$output .= '</div></div>';
	return $output;
}
add_shortcode( 'hitc_events_feed', 'add_hitc_events' );


// make dates work ok
function datetransmog($start,$end,$weekly=false){
	// set the dates in the correct format
	//$start = str_replace('T',' ',$start);
	//$start = $start . '-05:00';
	$startday = date('l, F dS',strtotime($start));
	$dayonly = date('l\s',strtotime($start));
	// working on way to fix timezome input
	$starttime = date('g:i a',strtotime($start)- 5*60*60); // 5hrs at the end to compensate for the way the dates are on the feed.
	
	if (isset($end) && !empty($end) ){
		$endday = date('l, F dS',strtotime($end));
		$endtime = date('g:i a',strtotime($end)- 5*60*60);
	} else {
		$endday = '';
		$endtime = '';
	}
	// if start and end are same day
	if($startday == $endday){
		if($weekly){ // regular weekly events like classes and services
			$thedate = '<div class="theday">'. $dayonly .'</div> <div class="thetime">'. $starttime .' - '.$endtime .'</div>';
		}else{
			$thedate = '<div class="theday">'. $startday .'</div> <div class="thetime">'. $starttime .' - '.$endtime .'</div>';
		}
	} else { // if start and end are different days
		$thedate = '<div class="theday1">'. $startday .'</div> <div class="theday1">'. $endday .'</div> ';
	}
	return $thedate;
	
}
?>