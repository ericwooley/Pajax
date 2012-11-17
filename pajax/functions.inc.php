<?php



# Reads the formatted settings from a file and returns an array of the settings

function pajaxReadSettings(){

	if(file_exists(PAJAXSETTINGFILE)){

		$settings = file_get_contents(PAJAXSETTINGFILE);

		return explode("||", $settings);

	}

	return $settings = array();



}



# checks if the request is an ajax request.

function isAjax() {

	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 

	($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));

}





#####################################################################

# Settings Functions

#

# Settings Key

# 0. links to load ajax from

# 1. Head to change.

# 2. Content to change.

# 3. Include jQuery?

# 4. Which animation Effect to use.

# 5. Which loading gif to use.

# 6. weather or not to disable smooth animation css.

# 7. weather the loading bar should be show to the left or right.

# 8. Save using ajax?

#####################################################################

function pajaxAjaxSave(){

	if($_GET['id'] != 'pajax') return;

	# creates a string of the new settings dilemeted by "||"

	installPajaxInTheme();

	if(isset($_REQUEST["linkDiv"])){

		$newSettings = 	 $_REQUEST["linkDiv"]."||" 			// Setting 0

						.$_REQUEST["headline"]."||"			// Setting 1

						.$_REQUEST["content"]."||" 			// Setting 2

						.$_REQUEST["incJQ"]."||"   			// Setting 3

						.$_REQUEST["effect"]."||"  			// Setting 4

						.$_REQUEST["gif"]."||"     			// Setting 5

						.$_REQUEST["disableSmoothCss"]."||"     // Setting 6

						.$_REQUEST["animationPosition"]."||"	// Setting 7

						.$_REQUEST["pajaxAjax"];				// Setting 8

		# Write the settings to the file.

		if(file_put_contents(PAJAXSETTINGFILE, $newSettings)){

			echo "Settings Updated";

			//print_r($_POST);echo "<br />";print_r(pajaxReadSettings());echo "<br />";

		}

		else

			echo "could not write file: ".PAJAXSETTINGFILE;

	}

	if(isAjax() && $_GET['id'] == 'pajax'){

		echo "Saved!";

		die();

	}

}



function installPajaxInTheme(){

	if(!isAjax() || !isset($_GET['pajaxThemeInstall']))return;

	$theme =$_GET['pajaxThemeInstall'];

	echo "<br/><b>***************************** modifying $theme *****************************</b><br />";

	//$theme="mycompany";

	$themeFiles = rglob("*.php", 0, GSTHEMESPATH.$theme);

	$cBefore = "echo '<span class=\"pajaxContent\">';";

	$tBefore = "echo '<span class=\"pajaxTitle\">';";

	$nBefore = "echo '<span class=\"pajaxNav\">';";

	$after = "echo '</span>';";



	$find = array(

			/*// Clean up (incase it is installed twice)

			$cBefore."get_page_content();".$after,

			$tBefore."get_page_title();".$after,

			$tBefore."get_page_clean_title();".$after,

			$nBefore."get_navigation();".$after,

			$nBefore."get_i18n_navigation();".$after,*/



			// Versions of get Content

			"get_page_content();",



			// Versions of get title

			"get_page_title();",

			"get_page_clean_title();",



			// Versions of get Navigation

			"get_navigation();",

			"get_navigation(return_page_slug());", 

			"get_i18n_navigation();",

			"my_get_navigation(return_page_slug(FALSE));",

			"get_navigation(get_page_slug(FALSE));",

			"get_nested_navigation();"

			 );



	$replace = array(

			// Versions of get Content

			$cBefore."get_page_content();".$after,



			// Versions of get Title

			$tBefore."get_page_title();".$after,

			$tBefore."get_page_clean_title();".$after,



			// Versions of get Navigation

			$nBefore."get_navigation();".$after,

			$nBefore."get_navigation(return_page_slug());".$after,

			$nBefore."get_i18n_navigation();".$after,

			$nBefore."my_get_navigation(return_page_slug(FALSE));".$after,

			$nBefore."get_navigation(get_page_slug(FALSE));".$after,

			$nBefore."get_nested_navigation();".$after

		);

	$time_start = microtime();



	foreach($themeFiles as $file)

		openReplace($file, $find, $replace);

		

	$time_end = microtime();

	$time = $time_end - $time_start;

	die("all theme files searched and modified in $time microseconds <br /><hr />");



}



# Opens Files and replaces the contents, but skips the head section. 

function openReplace($file, $find, $replace){



	echo "modifying $file";

	$contents = file_get_contents($file);



	// Avoid modifying the head of the file.

	if(preg_match("/<\/\bhead\b *>/", $contents)){

		//$contents = explode("</head>", $contents);

		$contents = preg_split("/<\/\bhead\b *>/", $contents);

		$head = $contents[0];

		$body = $contents[1];



		//Clean first, so you don't get doubles

		$body = str_replace($replace, $find, $body); 

		$contents = $head."</head>".str_replace($find, $replace, $body);

		echo " <b>Head File Dectected</b>";

	}

	else{

		$contents = str_replace($replace, $find, $contents);

		$contents = str_replace($find, $replace, $contents);

	}

	file_put_contents($file, $contents);

	echo "<br />";

}



# recursivly searches for files of a certain type

function rglob($pattern='*', $flags = 0, $path='')

{

    $paths=glob($path.'*', GLOB_MARK|GLOB_ONLYDIR|GLOB_NOSORT);

    $files=glob($path.$pattern, $flags);

    foreach ($paths as $path) { $files=array_merge($files,rglob($pattern, $flags, $path)); }

    return $files;

} 

