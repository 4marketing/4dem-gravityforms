<?php
/**
** Plugin Name: Gravity Forms - 4Dem Newsletter Add-On
** Plugin URI: http://www.gravityforms.com
** Description: Integrates Gravity Forms with 4Dem, allowing form submissions to be automatically sent to your 4Dem account
** Version: 1.0.0
** Author: 4marketing.it
** Author URI: http://www.4marketing.it
** Text Domain: gravityformsadvision-4dem
** Domain Path: /languages
** 
** ------------------------------------------------------------------------
** Copyright 2017 4marketing
** 
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
** 
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
** 
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 **/

define( 'GF_ADVISION_4DEM_VERSION', '1.0.0' );
define( 'GF_ADVISION_4DEM_API_ENDPOINT', 'https://api.4dem.it');

// If Gravity Forms is loaded, bootstrap the Advision_4Dem Add-On.
add_action( 'gform_loaded', array( 'GF_Advision_4Dem_Bootstrap', 'load' ), 5 );
/**
 * Class GF_Advision_4Dem_Bootstrap
 *
 * Handles the loading of the Advision_4Dem Add-On and registers with the Add-On Framework.
 */
class GF_Advision_4Dem_Bootstrap {
	
	/**
	 * If the Feed Add-On Framework exists, Advision_4Dem Add-On is loaded.
	 *
	 * @access public
	 * @static
	 */
	
	public static function load() {
		
		
		if ( ! method_exists( 'GFForms', 'include_feed_addon_framework' ) ) {
			
			return;
			
		}
		
		
		require_once( 'class-gf-advision-4dem.php' );
		
		
		GFAddOn::register( 'GFAdvision_4Dem' );
		
	}
	
}
/**
 * Returns an instance of the GFAdvision_4Dem class
 *
 * @see    GFAdvision_4Dem::get_instance()
 *
 * @return object GFAdvision_4Dem
 */
function gf_advision_4dem() {
	
	return GFAdvision_4Dem::get_instance();
	
}
