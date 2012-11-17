<?php
/*
Plugin Name: Pajax
Description: Loads a page via ajax.
Version: 1.4.5
Author: Eric Wooley
Author URI: http://www.ericwooley.com/
*/

# get correct id for plugin
$thisfile=basename(__FILE__, ".php");
# register plugin
register_plugin(
	$thisfile, //Plugin id
	'Pajax', 	//Plugin name
	'1.4.5', 		//Plugin version
	'Eric Wooley',  //Plugin author
	'http://www.ericwooley.com/', //author website
	'Handles ajax requests for content.', //Plugin description
	'theme', //page type - on which admin tab to display
	'pajaxSettings'  //main function (administration)
);

require_once(GSPLUGINPATH."pajax/functions.inc.php");


# add a link in the admin tab 'theme'
add_action('theme-sidebar','createSideMenu',array($thisfile,'Pajax Settings'));

# Call pajax just before the template loads
add_action('index-pretemplate','pajax');

# Adds the jquery to the theme header
//add_action('theme-header', 'pajaxLoaders');
add_action('index-posttemplate', 'pajaxLoaders');


add_action('admin-pre-header', 'pajaxAjaxSave');




# The path to the pajax settings file. It should be in GSDATAPATH
# but I found that getting permission to write to a file there was 
# inconsistant.
define('PAJAXSETTINGFILE', GSPLUGINPATH."pajax/pajax-settings.txt");

# Allows the user to edit the settings in the admin "theme" section.
function pajaxSettings(){
	// This got way to long to all be in one file.
	include(GSPLUGINPATH.'pajax/pajaxSettings.inc.php');
}


#####################################################################
# Pajax Page Manipulation Functions
#####################################################################

# Sets the theme loaders 
function pajaxLoaders(){
	// This got way to long to all be in one file.
	include(GSPLUGINPATH.'pajax/pajaxLoaders.inc.php');
}

# When the page is an ajax call, only return the page contents and the kill the script.
function pajax(){
	if(isAjax() && isset($_POST['pajax'])){
		echo "<!-- Loaded via Pajax -->";
		echo get_page_content();
		// Please let me know if you know of a better way to do this than die().
		die();	
	}
}