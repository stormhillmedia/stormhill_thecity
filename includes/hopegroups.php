<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/*****************************/
/*****************************/
/**** list hope groups by ****/
/*****************************/
/*****************************/

function shcaa_list_hope_groups($type, $showmap = 'true', $showchart = 'true'){
	// create the city object
	
	$ca = new CityApi();
	$ca->debug = false;
	$ca->json = false;	
	
	// set the args
	// args array can include
	/*
	page = [1,2,3,4,...]
	search = ["downtown" | etc] <- optional group name search
	under_group_id = [ 1234 | etc ] <- defaults to church group's ID
	group_types = ["CG" | "Service" | "Campus" | etc] <- defaults to all group types
	include_inactive = [ true | false ] <- defaults to false
	include_addresses = [ true | false ]
	include_composition = [ true | false ]
	include_user_ids = [ true | false ]
	
	*/
	
	// cache the results
	// attempt to get cached request
	$transient_key = "_shcaa_hope_groups_feed";
	// If cached (transient) data are used, output an HTML
	// comment indicating such
	
	$cached = get_transient( $transient_key );
	if ( false !== $cached ) {
		$mygrps = $cached; 
	} else {
		
		// create array of the groups filtered by the args and include the tags - for the call to theCITY api
		$args = array();
		$args['group_types'] = 'Hope';
		$args['include_addresses'] = 'true';
	
		$groups = $ca->groups_index($args); 
		
		$mygrps = array();
		$grps = $groups['groups'];
		$i = 0;
		foreach ($grps as $grp){
			$mygrps[$i]['grp_id'] = $grp['id'];
			$mygrps[$i]['parentid'] = $grp['parent_id'];
			
			$mygrps[$i]['image'] = $grp['profile_pic'];
			$mygrps[$i]['name'] = $grp['name'];
			$mygrps[$i]['description'] = $grp['external_description'];
			$mygrps[$i]['neighborhood'] = $grp['nearest_neighborhood_name'];
			$mygrps[$i]['invite'] = $grp['default_invitation_custom_message'];
			$mygrps[$i]['cityurl'] = $grp['internal_url'];
			
			// address fields
			if (isset($grp['addresses'][0]) && !empty($grp['addresses'][0]) ){
				$mygrps[$i]['add_name'] = $grp['addresses'][0]['friendly_name'];
				$mygrps[$i]['add_street'] = $grp['addresses'][0]['street'];
				$mygrps[$i]['add_street2'] = $grp['addresses'][0]['street2'];
				$mygrps[$i]['add_city'] = $grp['addresses'][0]['city'];
				$mygrps[$i]['add_state'] = $grp['addresses'][0]['state'];
				$mygrps[$i]['add_zipcode'] = $grp['addresses'][0]['zipcode'];
				$mygrps[$i]['add_longitude'] = $grp['addresses'][0]['longitude'];
				$mygrps[$i]['add_latitude'] = $grp['addresses'][0]['latitude'];
			}
			
			// Get Tags
			$tags = $ca->groups_tags_index($grp['id']); 
			if ($tags){
				foreach ($tags as $tag){
					if (is_array($tag)){
						$mygrps[$i]['mytags'] = array();
						foreach ($tag as $el){
							$mygrps[$i]['mytags'][] = $el;
						} // end inner foreach
					} // end if
				} // end foreach
			} // end if tags
			
			// Get Users
			$users = $ca->groups_roles_index($grp['id']); 
			
			if ($users){
				foreach ($users as $user){
					if (is_array($user)){
						$mygrps[$i]['leaders'] = array();
						$lc = 0;
						foreach ($user as $usr){
							
							if($usr['title'] == 'Leader'){

								$mygrps[$i]['leaders'][$lc]['name'] = $usr['user_name'];

								// get contact info
								$userinfo = $ca->users_show($usr['user_id']);
								$mygrps[$i]['leaders'][$lc]['phone'] = $userinfo['primary_phone'];
								$mygrps[$i]['leaders'][$lc]['email'] = $userinfo['email'];
								$mygrps[$i]['leaders'][$lc]['first'] = $userinfo['first'];
								$mygrps[$i]['leaders'][$lc]['last'] = $userinfo['last'];
								$mygrps[$i]['leaders'][$lc]['nickname'] = $userinfo['nickname'];
								
								// group family members
								$userfam = $ca->users_family_index($usr['user_id']);
								$mygrps[$i]['leaders'][$lc]['fam_id'] = $userfam['external_id'];
								$lc++;								
							}
						} // end inner foreach
					} // end if
				} // end foreach
			} // end if users
			$i++;
		} // end foreach
		//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mygrps ); print_r('</pre>'); 

		set_transient( $transient_key, $mygrps, 60*60*24*7 ); // seven days
	} // end if not cached
	
	$output = ''; 
	// group map output
	
	if ($showmap != 'false'){
		$output .= '<div class="hf-g-map"></div>';//output_google_map();
	}
	// group chart output
	if ($showchart != 'false'){
		$output .= '<table class="groupslist">'; 
		$output .= '<tr>
		  <th>Leader</th>
		  <th>Day/Time</th>
		  <th>Location</th>
		  <th>Contact</th>
		</tr>';
	} else {
		$output .= '<div class="groupslist">'; 
	} // end if showchart 

	$cnt = 0;
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mygrps ); print_r('</pre>'); 
	foreach ($mygrps as $grpkey => $mygrp){
		$grp_id = $mygrp['grp_id'];
		// get the tags array and start making choices
		$dtags = $mygrp['mytags'];	
		$meta = sh_find_groupmeta($dtags);
		
		$leaders = $mygrp['leaders'];
		
		// stuff from meta
		$hgtype = $meta['type'];
		$day = $meta['day'];
		$time = $meta['time'];
		$extras = $meta['extra']; // array
		// is kid friendly
		$prokid = 0;
		if (in_array('kid-friendly',$extras)){
			$prokid = 1;
		}
		$name = $mygrp['name'];
		$name = str_replace('HG:','',$name);
		//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $name ); print_r( '--<br />--' ); print_r( $meta); print_r('------------</pre>'); 
		
		// Leader information
		$ldrs = $mygrp['leaders'];
		$leaders = '<div class="leaders">';
		$famids = array();
		$leds = array();
		foreach ($ldrs as $k => $ldr){
			// search the famids for previous use of same famid
			$key = array_search($ldr['fam_id'],$famids);
			
			if(is_numeric($key)){
				$nnic = $ldrs[$key]['nickname'];
				$nfirst = $ldrs[$key]['first'];
				$nlast = $ldrs[$key]['last'];
				$phone = $ldrs[$key]['phone'];
				$email = $ldrs[$key]['email'];
				
				//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $key ); print_r('</pre>');
				$fn1 = (isset($nnic) && !empty($nnic) ? $nnic : $nfirst);
				$fn2 = (isset($ldr['nickname']) && !empty($ldr['nickname']) ? $ldr['nickname'] : $ldr['first']);
				$fullname = $fn1 .' & '. $fn2 .' '.$nlast;
				unset($leds[$key]);
			} else {
				$first = (isset($ldr['nickname']) && !empty($ldr['nickname']) ? $ldr['nickname'] : $ldr['first'] );
				$fullname = $first .' '.$ldr['last'];
				$phone = $ldr['phone'];
				$email = $ldr['email'];
			} // end if
			$leds[] = array(
				'fullname' => $fullname,
				'phone' => $phone,
				'email' => $email
			);
			// store the fam id 
			$famids[] = $ldr['fam_id'];
		} // end foreach
		// output
		$numled = count($leds);
		$lcntr = 1;
		foreach ($leds as $key => $led ){
			$leaders .= '<span class="leader">';
				$leaders .= $led['fullname'];
			$leaders .= '</span>';
			if($numled !== $lcntr){
				$leaders .= ', ';
			}
			$lcntr++;
		}
		$leaders .= '</div>';
		
		$neighborhood = $mygrp['neighborhood'];
		$neighborhood = str_replace('Neighborhood:','',$neighborhood);
		
		$grpfriendname = $mygrp['add_name'];
		
		$grp_name = (isset($grpfriendname) && !empty($grpfriendname) ? $grpfriendname : $neighborhood);
		
		// map coords
		$lng = $mygrp['add_longitude'];
		$lat = $mygrp['add_latitude'];
		
		// contact button
		$contactform = '<form id="hope-group-contact" action="/hope-group-contact">
							<input value="'.$grpkey.'" id="group" name="group" type="hidden">
							<input type="submit" value="Contact Leaders">
						</form>';
		unset($leds);
		
		// Allow output of only the hopegroup type requested in the shortcode, if parameter is there
		if ($showchart != 'false'){
		  if($type=='ALL' || $type == $hgtype){
			  $output .= '<tr>';
			  // name
			  if (isset($leaders) && !empty($leaders) ){
				  $output .= '<td class="grp-name">'.$leaders.'</td>';
			  }
			  // Day - time
			  if (isset($day) && !empty($day) && $day!='TBD' ){
				  $daytime = '<td class="grp-day">'.$day.' '.$time.'</div>';
			  } else {
				   $daytime = '<td class="grp-day">To Be Determined</div>';
			  } 
			  $output .= $daytime;
			  // neighborhood
			  if (isset($grp_name) && !empty($grp_name) ){
				  $output .= '<td class="grp-location">'.$grp_name.'</td>';
			  }
	  
			   // contact
			  $output .= '<td class="grp-contact">';
			  $output .= $contactform;
			  // stuff for the map markers
			  $mleaders = html_entity_decode($name);
			  
			  $output .= '
			  <div class="marker" style="display:none;" data-lat="'.$lat.'" data-lng="'.$lng.'">
				  <div class="output">
					  <div class="mled">'.$mleaders.'</div>
					  <div class="mday">'.$day.' '.$time.'</div>
				  </div>
				  <div class="kids">'.$prokid.'</div>
				  <div class="zone">'.$hgtype.'</div>
			  </div>';
			  $output .= '</td>';
				 
			  $output .= '</tr>';
		  } // end if type
		  
		} else { // end if showchart
			  if (isset($day) && !empty($day) && $day!='TBD' ){
				  $daytime = '<td class="grp-day">'.$day.' '.$time.'</div>';
			  } else {
				   $daytime = '<td class="grp-day">To Be Determined</div>';
			  } 

			$mleaders = html_entity_decode($name);
			$output .= '
			<div class="marker" style="display:none;" data-lat="'.$lat.'" data-lng="'.$lng.'">
				<div class="output">
					<div class="mled">'.$mleaders.'</div>
					<div class="mday">'.$day.' '.$time.'</div>
				</div>
				<div class="kids">'.$prokid.'</div>
				<div class="zone">'.$hgtype.'</div>
			</div>';
		}

	  } // end foreach
	  
	  if ($showchart != 'false'){
	  	$output .= '</table>';
	  } else {
		  $output .= '</div>';
	  }

	return $output;
}	

function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['fam_id'] === $id) {
           return $key;
       }
   }
   return null;
}

// get the meta info for a group by searching through the tags;

function sh_find_groupmeta($dtags){
	// make array to capture tags that are left over
	$extra = array();
	if (isset($dtags) && !empty($dtags) ){
		foreach ($dtags as $tag){
			$name = strtolower(trim($tag['name']));
			
			switch ($name) {
				// find the group type
				case "family":
					$type = 'Family';
					break;
				case "college":
					$type = 'College';
					break;
				case "young professional":
					$type = 'Young Professionals';
					break;
				case "yopro":
					$type = 'Young Professionals';
					break;
				case "youth":
					$type = 'Youth';
					break;
				case "family":
					$type = 'Family';
					break;
				// get the day
				case "sunday":
					$day = 'Sunday';
					break;
				case "monday":
					$day = 'Monday';
					break;
				case "tuesday":
					$day = 'Tuesday';
					break;
				case "wednesday":
					$day = 'Wednesday';
					break;
				case "thursday":
					$day = 'Thursday';
					break;
				case "friday":
					$day = 'Friday';
					break;
				case "saturday":
					$day = 'Saturday';
					break;
				// Get the day time
				case "afternoon":
					$time = 'Afternoon';
					break;
				case "evening":
					$time = 'Evening';
					break;
				default:
					$extra[] = $name;
					break;
	
			} // end switch
		} // end tags foreach
		
		if( !isset($type) || empty($type) ){
			$type = 'Family';
		}
		
		if( !isset($day) || empty($day) ){
			$day = 'TBD';
		}
		
		if( !isset($time) || empty($time) ){
			$time = 'Evening';	
		}
		
		$meta = array('type' => $type, 'day' => $day, 'time' => $time, 'extra' => $extra);
	} else { // end if tags
		$meta = array('type' => 'Family', 'day' => 'TBD', 'time' => 'Evening', 'extra' => array());
	}
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $name ); print_r('</pre>');
	return $meta;
}

// Add Shortcode 
// calls the output function from the plugin
function add_hopegroups($atts){
	$a = shortcode_atts( array(
		'type' => 'ALL',
		'showmap' => 'true',
		'showchart' => 'true',
    ), $atts );
	$output = '';
	$output .= '<div class="hopegroups-wrapper">';
	$output .= '<div class="inner">';
	if(isset($a['heading']) && !empty($a['heading']) ){
		$output .= '<h2>'.$a['heading'].'</h2>';
	}
	
	$output .= shcaa_list_hope_groups($a['type'],$a['showmap'],$a['showchart']);
	$output .= '</div></div>';
	return $output;
}
/*
Hopegroup types are 
College
Family
Young Professionals
Youth
*/
add_shortcode( 'hopegroup_feed', 'add_hopegroups' );


// hopegroup contact form tweaks to get the user info into the ninja form

function ninja_forms_register_hg_leaderinfo(){
  add_action( 'ninja_forms_pre_process', 'ninja_forms_get_hg_leaderinfo' );
}
add_action( 'init', 'ninja_forms_register_hg_leaderinfo' );
function ninja_forms_get_hg_leaderinfo(){
  global $ninja_forms_processing;
  
  //Get the user submitted value for the field with an ID of 11. 
  $grpID = $ninja_forms_processing->get_field_value( 11 );
  if ( isset($grpID) && !empty($grpID) ) {
	  // get the group leader array
	  $transient_key = "_shcaa_hope_groups_feed";
	  $leaders = get_transient( $transient_key );
	  $grpleds = $leaders[$grpID]['leaders'];
	  $mes = 'Please Contact the Following Leader(s) with this information request:'."\r\n\r\n";
	  foreach ($grpleds as $led){
		  $mes .= $led['name'] .' | '.$led['email'] .' | '.$led['phone'] ."\r\n";
	  }
	  $mes .= ''."\r\n\r\n";
	  $mes .= 'Copy and paste this email list to save time'."\r\n\r\n";
	  foreach ($grpleds as $led){
		  $mes .= $led['email'] .', ';
	  }
	
	  $user_value = $mes;
	  
	  //Update the user submitted value for the field with an ID of 3:
	  $ninja_forms_processing->update_field_value( 11, $user_value );
  } // end if
}


// Google Maps
// map
function output_google_map(){
	$dt_map_coords_lat = '30.368496';
	$dt_map_coords_lng = '-97.743135';
	if ( (isset($dt_map_coords_lat) && !empty($dt_map_coords_lat)) && (isset($dt_map_coords_lng) && !empty($dt_map_coords_lng)) ) {
		$map = add_the_google_map($dt_map_coords_lat,$dt_map_coords_lng);
	}
	return $map;
}

// Google Maps template
function add_the_google_map($lat,$lng){
	$code = '<div id="mapbox">';
	$code .= '<input type="hidden" id="map-coords-lat" value="'.$lat.'" />';
	$code .= '<input type="hidden" id="map-coords-lng" value="'.$lng.'" />';
	$code .= '<div id="map-canvas"></div>';
	$code .= '</div>';
	return $code; 	
}


/*

function shcaa_list_hope_groups_full(){
	// create the city object
	$ca = new CityApi();
	$ca->debug = false;
	$ca->json = false;	
	
	// set the args
	// args array can include
	
	// cache the results
	// attempt to get cached request
	$transient_key = "_shcaa_hope_groups_feed";
	// If cached (transient) data are used, output an HTML
	// comment indicating such
	$cached = get_transient( $transient_key );
	if ( false !== $cached ) {
		$mygrps = $cached;
	} else {
		
		// create array of the groups filtered by the args and include the tags
		$args = array();
		$args['group_types'] = 'Hope';
		$args['include_addresses'] = 'true';
	
		$groups = $ca->groups_index($args); 
		
		$mygrps = array();
		$grps = $groups['groups'];
		$i = 0;
		foreach ($grps as $grp){
			$mygrps[$i]['grp_id'] = $grp['id'];
			$mygrps[$i]['parentid'] = $grp['parent_id'];
			
			$mygrps[$i]['image'] = $grp['profile_pic'];
			$mygrps[$i]['name'] = $grp['name'];
			$mygrps[$i]['description'] = $grp['external_description'];
			$mygrps[$i]['neighborhood'] = $grp['nearest_neighborhood_name'];
			$mygrps[$i]['invite'] = $grp['default_invitation_custom_message'];
			$mygrps[$i]['cityurl'] = $grp['internal_url'];
			
			// address fields
			if (isset($grp['addresses'][0]) && !empty($grp['addresses'][0]) ){
				$mygrps[$i]['add_name'] = $grp['addresses'][0]['friendly_name'];
				$mygrps[$i]['add_street'] = $grp['addresses'][0]['street'];
				$mygrps[$i]['add_street2'] = $grp['addresses'][0]['street2'];
				$mygrps[$i]['add_city'] = $grp['addresses'][0]['city'];
				$mygrps[$i]['add_state'] = $grp['addresses'][0]['state'];
				$mygrps[$i]['add_zipcode'] = $grp['addresses'][0]['zipcode'];
				$mygrps[$i]['add_longitude'] = $grp['addresses'][0]['longitude'];
				$mygrps[$i]['add_latitude'] = $grp['addresses'][0]['latitude'];
			}
			
			// Get Tags
			$tags = $ca->groups_tags_index($grp['id']); 
			if ($tags){
				foreach ($tags as $tag){
					if (is_array($tag)){
						$mygrps[$i]['mytags'] = array();
						foreach ($tag as $el){
							$mygrps[$i]['mytags'][] = $el;
						} // end inner foreach
					} // end if
				} // end foreach
			} // end if tags
			$i++;
		} // end foreach
		//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mygrps ); print_r('</pre>');
		set_transient( $transient_key, $mygrps, 60*60*12 );
	} // end if not cached
	
	// group output
	$output = '<ul class="groupslist">';
	$cnt = 0;
	//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $mygrps ); print_r('</pre>');
	foreach ($mygrps as $mygrp){
		$grp_id = $mygrp['grp_id'];
		$parentid = $mygrp['parentid'];

		// get the tags array and start making choices
		$dtags = $mygrp['mytags'];	

		$meta = sh_find_groupmeta($dtags);
//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $meta); print_r('</pre>');
		
		// stuff from meta
		$hgtype = $meta['type'];
		$day = $meta['day'];
		$time = $meta['time'];
		$tags = $meta['extra'];
		
		$image = $mygrp['image'];
		$name = $mygrp['name'];
		$description = $mygrp['description'];
		$neighborhood = $mygrp['neighborhood'];
		$invite = $mygrp['invite'];
		$cityurl = $mygrp['cityurl'];
		
		// address fields
		
		$add_name = $mygrp['add_name'];
		$add_street = $mygrp['add_street'];
		$add_street2 = $mygrp['add_street2'];
		$add_city = $mygrp['add_city'];
		$add_state = $mygrp['add_state'];
		$add_zipcode = $mygrp['add_zipcode'];
		$add_longitude = $mygrp['add_longitude'];
		$add_latitude = $mygrp['add_latitude'];
						
		if (is_array($tags) && !empty($tags)){
			//print_r('<pre style="padding: 10px; border: 1px solid #000; margin: 10px">'); print_r( $dtags ); print_r('</pre>');
			$mytags = '<div class="grp-tags"><h4>Tags: </h4>';
			foreach ($tags as $dtag){
				$mytags .= '<span> | '.$dtag.' | </span>';					
			} // end foreach
			$mytags .= '</div>';
		} else {
			$mytags = '';
		}

		$output .= '<li class="group">';
		  // image
		  if (isset($image) && !empty($image) ){
			  $output .= '<div class="grp-image"><img src="'.$image.'" alt="'.$name.'" title="'.$name.'" /></div>';
		  }
		  // name
		  if (isset($name) && !empty($name) ){
			  $output .= '<div class="grp-name">'.$name.'</div>';
		  }
		  // description
		  if (isset($description) && !empty($description) ){
			  $output .= '<div class="grp-description">'.$description.'</div>';
		  }
		  // Invitation
		  if (isset($invite) && !empty($invite) ){
			  $output .= '<div class="grp-invite">'.$invite.'</div>';
		  }
		  // type
		  if (isset($type) && !empty($type) ){
			  $output .= '<div class="grp-type">Type: '.$type.'</div>';
		  }
		  
		  // Day - time
		  if (isset($day) && !empty($day) && $day!='TBD' ){
			  $output .= '<div class="grp-type">When: '.$day.' '.$time.'</div>';
		  }
		  
		  // neighborhood
		  if (isset($neighborhood) && !empty($neighborhood) ){
			  $output .= '<div class="grp-neighborhood">'.$neighborhood.'</div>';
		  }
		  
		  // Location
		  $output .= '<h3>Location</h3>';
		  // location name
		  if (isset($add_name) && !empty($add_name) ){
			  $output .= '<div class="grp-add-name">'.$add_name.'</div>';
		  }
		  $output .= '<div class="grp-add">';
			  // location street
			  if (isset($add_street) && !empty($add_street) ){
				  $output .= '<div class="grp-add-street">'.$add_street.', </div>';
			  }
			  // location street 2
			  if (isset($add_street2) && !empty($add_street2) ){
				  $output .= ' <div class="grp-add-street2">'.$add_street2.', </div> ';
			  }
			  // location city
			  if (isset($add_city) && !empty($add_city) ){
				  $output .= ' <div class="grp-add-city">'.$add_city.' </div> ';
			  }
			  // location state
			  if (isset($add_state) && !empty($add_state) ){
				  $output .= ' <div class="grp-add-state">'.$add_state.', </div> ';
			  }
			  // location zip
			  if (isset($add_zipcode) && !empty($add_zipcode) ){
				  $output .= ' <div class="grp-add-zipcode">'.$add_zipcode.' </div> ';
			  }
		  // end address box
		  $output .= '</div>';
		  
		  // group tags
		  if ( isset($mytags) && !empty($mytags)  ){
			  $output .= $mytags;
		  }

		  // location coords
		  if ( (isset($add_longitude) && !empty($add_longitude)) && (isset($add_latitude) && !empty($add_latitude)) ){
			  $output .= '<div class="grp-add-coords">'.$add_longitude.','.$add_latitude .', </div>';
		  }
			  
		  
	  $output .= '</li>';
	}
	$output .= '</ul>';
	
	return $output;
}
*/
?>