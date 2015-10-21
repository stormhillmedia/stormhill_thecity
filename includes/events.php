<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/*****************************/
/*****************************/
/**** list events ****/
/*****************************/
/*****************************/


function shcaa_list_events($eventtag='',$groupname=''){	
	// attempt to get cached request
	if(isset($groupname) && !empty($groupname) ){
		$transient_key = "_shcaa_events_feed_".$groupname;
	} else {
		$transient_key = "_shcaa_events_feed_all";
	}
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
		$the_city = new TheCity('theCityUsername');
		if(isset($groupname) && !empty($groupname) ){
			$the_city->set_group_nickname($groupname); 
		}
		$events = $the_city->events(50);
	  
		$event_titles = $events->titles(); 
			
		$myevents = array();
		$i = 0;
		foreach($event_titles as $indx => $eve) {  
			// get the object
			$rawevent = $events->select($indx);
			
			// get the body
			$event = accessProtected($rawevent, 'data');
		//print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 11px; margin: 10px">'); print_r( $event ); print_r('</pre>'); 
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
	
	// find equipping classess and add extra date modifications
	  $myevents = add_event_date_info($myevents);

	  $output = '<ul class="eventlist">';
	  $cnt = 0; 
	  //print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 11px; margin: 10px">'); print_r( $myevents ); print_r('</pre>');
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
			 // if(!in_array($title,$dupcheck)) {   // turned off the dupcheck feature for all events
			  
				  $starting_at = $myeve['starting_at'];
				  $ending_at = $myeve['ending_at'];
				  $dateinfo = (isset($myeve['dateinfo']) && !empty($myeve['dateinfo']) ? $myeve['dateinfo'] : '');
				  $event_items = $myeve['event_items']; // array - may contain images??
				  $body = $myeve['body'];
				  $short_url = $myeve['short_url'];
				  
				  // dates
				  if ( isset($starting_at) && !empty($starting_at) ){
					  if( $tag == 'Equipping Class' || $tag == 'Sunday Morning Worship'){
					  	$dates = datetransmog($starting_at,$ending_at,$dateinfo,true);
					  } else {
						 $dates = datetransmog($starting_at,$ending_at);
					  }
				  }
				  // if this is a class
				  if ($tag == 'Equipping Class'){
					  if(!in_array($title,$dupcheck)) {
						$stitle = str_replace('| Equipping Class','',$title);
						$output .= '<li class="event eclass">';
							$output .= '<table class="blanktable">';
							$output .= '<tr>';
							$output .= '<td class="month">'.$dates['month'].'</td>';
							$output .= '<td class="title"><h3 class="eve-title">'.$stitle.'</h3></td>';
							$output .= '</tr>';
							$output .= '<tr>';
							$output .= '<td class="datelabel">Dates:</td>';
							$output .= '<td class="dates"><div class="eve-days">'.$dates['days'].'</div></td>';
							$output .= '</tr>';
							$output .= '<tr>';
							$output .= '<td class="timelabel">Time:</td>';
							$output .= '<td class="time"><div class="eve-time">'.$dates['time'].'</div></td>';
							$output .= '</tr>';
							$output .= '</table>';
							// description
							if (isset($body) && !empty($body) ){
								$output .= '<div class="eve-description">'.$body.'</div>';
							}
							// link
							if (isset($short_url) && !empty($short_url) ){
								$output .= '<a href="'.$short_url.'" target="_blank" class="eve-link button">Learn More and Register</a>';
							}
						$output .= '</li>';	
					  }
				} else {
					$output .= '<li class="event">';
						// name
						if (isset($title) && !empty($title) ){
							$output .= '<h3 class="eve-title">'.$title.'</h3>';
						}
						// dates
						if (isset($dates['month']) && !empty($dates['month']) ){
							$output .= '<div class="eve-month">'.$dates['month'].'</div>';
						}
						if (isset($dates['days']) && !empty($dates['days']) ){
							$output .= '<div class="eve-days">'.$dates['days'].'</div>';
						}
						if (isset($dates['time']) && !empty($dates['time']) ){
							$output .= '<div class="eve-dates">'.$dates['time'].'</div>';
						}
						// description
						if (isset($body) && !empty($body) ){
							$output .= '<div class="eve-description">'.$body.'</div>';
						}
						// link
						if (isset($short_url) && !empty($short_url) ){
							$output .= '<a href="'.$short_url.'" target="_blank" class="eve-link button">Learn More</a>';
						}
					$output .= '</li>';
				} // end if
				
			// } // end if dupcheck
	  } // end if in tag
	  $dupcheck[] = $title;
	  } // end foreach
	 
	  $output .= '</ul>';
	
	return $output;
}

function add_event_date_info($events){
	$eclasses = array();
	$ecc = 0;
	foreach ($events as $ekey => $eve){
		$title = trim($eve['title']);
		if (strpos($title,'Equipping Class') !== false) {
			$date = datesimple($eve['ending_at']);
			$eclasses[$title][] = array(
				'date' => $date,
				'ekey' => $ekey
			);
		} // end if
	} // end foreach
	
	// go thru new array and find first and last
	foreach ($eclasses as $dkey => $dates){
		
		end($dates); // move the internal pointer to the end of the array
		$endkey = key($dates);
		$days = '';
		foreach ($dates as $key => $date){
			$lastmonth = (isset($month) && !empty($month) ? $month : '');
			$month = date('M',strtotime($date['date']));
			$day = date('d',strtotime($date['date']));
			if ($key == 0){
				$startmonth = $month;
			} elseif ($key == $endkey){
				$endmonth = $month;
			}
			if($month != $lastmonth){
				$days .= $month.' ';
			}
			$days .= $day;
			if($key != $endkey){
				$days .= ', ';
				// unset all event records of this name except the last one
				unset($events[$date['ekey']]);
			} else {
				$days .= ' ';
				$mykey = $date['ekey'];
			}
		} // end foreach
		if($startmonth == $endmonth){
			$months = $startmonth;
		} else { 
			$months =  $startmonth.' / '.$endmonth;
		}
		$dateinfo = array(
			'name' => $dkey,
			'month' => $months,
			'days' => $days
		);
		$events[$mykey]['dateinfo'] = $dateinfo;
		
	} // end foreach
	
	// print_r('<pre style="padding: 10px; border: 1px solid #000; font-size: 11px; margin: 10px">'); print_r( $events ); print_r('</pre>');
	
return $events;

}


// Add Shortcode 
// calls the output function from the plugin
function add_hitc_events($atts){
	$a = shortcode_atts( array(
		'heading' => '',
		'eventtag' => '',
		'group_nickname' => '',
    ), $atts );
	$output = '';
	$output .= '<div class="events-wrapper">';
	$output .= '<div class="inner">';
	if(isset($a['heading']) && !empty($a['heading']) ){
		$output .= '<h2>'.$a['heading'].'</h2>';
	}
	$output .= shcaa_list_events($a['eventtag'],$a['group_nickname']);
	$output .= '</div></div>';
	return $output;
}
add_shortcode( 'hitc_events_feed', 'add_hitc_events' );




// make dates work ok

function datesimple($end){
	// set the dates in the correct format
	$date = date('Y-m-d',strtotime($end)- 5*60*60); // 5hrs at the end to compensate for the way the dates are on the feed.
	return $date;
}

function datetransmog($start,$end,$dateinfo='',$weekly=false){
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $start ); print_r('</pre>');
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $end ); print_r('</pre>');
	// set the dates in the correct format
	//$start = str_replace('T',' ',$start);
	//$start = $start . '-05:00';
	//$startday = date('l, F dS',strtotime($start)- 5*60*60);
	date_default_timezone_set('America/Chicago');
	$startday = date('l, F dS',strtotime($start));
	$dayonly = date('l\s',strtotime($start));
	// working on way to fix timezome input
	$starttime = date('g:i a',strtotime($start)); // 5hrs at the end to compensate for the way the dates are on the feed.
	
	if (isset($end) && !empty($end) ){
		$endday = date('l, F dS',strtotime($end));
		$endtime = date('g:i a',strtotime($end));
	} else {
		$endday = '';
		$endtime = '';
	}
	$thedate = array();
	// if start and end are same day
	if($startday == $endday){
		if($weekly){ // regular weekly events like classes and services
			if( isset($dateinfo) && !empty($dateinfo) ){
				$thedate['month'] = '<div class="themonth">'.$dateinfo['month'].'</div>';
				$thedate['days'] = '<div class="thedays">'.$dateinfo['days'].'</div>';
				$thedate['time'] = '<div class="thetime">'. $starttime .' - '.$endtime .'</div>';
			} else {
				$thedate['time'] = '<div class="theday">'. $dayonly .'</div> <div class="thetime">'. $starttime .' - '.$endtime .'</div>';
			}
		} else {
			$thedate['time'] = '<div class="theday">'. $startday .'</div> <div class="thetime">'. $starttime .' - '.$endtime .'</div>';
		}
	} else { // if start and end are different days
		$thedate['time'] = '<div class="theday1">'. $startday .'</div> <div class="theday1">'. $endday .'</div> ';
	}
	return $thedate;
	
}

// flush the caches
function flush_event_transients(){
	if (is_admin() && $_GET['eventcache'] == 'flushcaches'){
		$caches = array(
			'_shcaa_events_feed_all',
			'_shcaa_events_feed_h242',
		);	
		$notes = '';
		foreach($caches as $cache){
			delete_transient( $cache );
			$notes .= '<div class="updated"><p>Deleted '.$cache.' from Caches</p></div>';
		}
		$note = '<div class="updated"><p>All Done</p></div>';
		 add_action('admin_notices', function() { echo $note;} );    

	}
}
add_action('admin_init','flush_event_transients');

?>