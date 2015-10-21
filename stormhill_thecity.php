<?php
/*
Plugin Name: Stormhill theCity Admin API interface
Plugin URI: http://www.stormhillmedia.com
Description: leveraages theCity API into a wordpress plugin with functions to output groups, events and more.
Version: 0.4
Author: Jim Camomile - Stormhillmedia
Author URI: http://www.stormhillmedia.com
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/
/*  Copyright 2015 Stormhillmedia

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// include theCity Admin api library files
include('thecity_admin_api/lib/ca-main.php'); 

// include theCity Plaza api library files
function accessProtected($obj, $prop) {
  $reflection = new ReflectionClass($obj);
  $property = $reflection->getProperty($prop);
  $property->setAccessible(true);
  return $property->getValue($obj);
}
include('thecity-plaza_api/lib/the_city.php');	

// include the hopegroups function
include('includes/hopegroups.php'); 

// include the events function
include('includes/events.php'); 

// include the events function
include('includes/testimonies.php'); 

// Add Jquery support
function shcaa_js_scripts() {
	if ( !is_admin() && 'page' == get_post_type() ) {
		wp_enqueue_script( 'pw_google_maps_api', '//maps.googleapis.com/maps/api/js', array(), null );
		wp_enqueue_script('shcaa_custom', plugins_url('js/shcaa_custom.js',__FILE__ ) , array('jquery') );
	}
}
add_action('wp_enqueue_scripts', 'shcaa_js_scripts');

// Plugin CSS
function shcaa_css_load() {
	wp_enqueue_style('shcaa-styles', apply_filters('shcaa_css', plugins_url('css/shcaa-styles.css', __FILE__)));
}
add_action( 'wp_enqueue_scripts', 'shcaa_css_load', 20 );

// CSS for Admin
function shcaa_admin_css_load() {
	wp_enqueue_style('shcaa-admin-styles', plugins_url( 'css/shcaa-admin.css',  __FILE__ ));	
}
add_action( 'admin_enqueue_scripts', 'shcaa_admin_css_load', 20 );


// formatting functions from api library
/**
 * test-util.php
 * 
 * Utility functions used for testing the CityApi class
 */
 
 /**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 *
 * From: http://recursive-design.com/blog/2008/03/11/format-json-with-php/
 */
function format_json($json) {
	
	$result      = '';
	$pos         = 0;
	$strLen      = strlen($json);
	$indentStr   = '  ';
	$newLine     = "\n";
	$prevChar    = '';
	$outOfQuotes = true;
	
	for ($i=0; $i<=$strLen; $i++) {
	
		// Grab the next character in the string.
		$char = substr($json, $i, 1);
	
		// Are we inside a quoted string?
		if ($char == '"' && $prevChar != '\\') {
			$outOfQuotes = !$outOfQuotes;
		
		// If this character is the end of an element, 
		// output a new line and indent the next line.
		} else if(($char == '}' || $char == ']') && $outOfQuotes) {
			$result .= $newLine;
			$pos --;
			for ($j=0; $j<$pos; $j++) {
				$result .= $indentStr;
			}
		}
		
		// Add the character to the result string.
		$result .= $char;
	
		// If the last character was the beginning of an element,  
		// output a new line and indent the next line.
		if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
			$result .= $newLine;
			if ($char == '{' || $char == '[') {
				$pos ++;
			}
			
			for ($j = 0; $j < $pos; $j++) {
				$result .= $indentStr;
			}
		}
		
		$prevChar = $char;
	}
	
	return $result;
}

?>