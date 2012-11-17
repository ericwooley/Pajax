<?php 
	global $SITEURL;
	#load the pajax settings
	$s = pajaxReadSettings();

	# assign settings to variables
	$nav = $s[0];
	$headline = $s[1];
	$content = $s[2];
	$jq = $s[3];
	$effect = explode(" / ", $s[4]);
	$lGif = $s[5];
	$disableSmoothCss = $s[6];
	$gifPosition = $s[7];

	if(strlen($disableSmoothCss) < 1){
		?>
		<style>
			html{overflow:scroll;}
		</style>
		<?php
	}

	# include jquery if the user wants it.
	if(strlen($jq)>1){
		?>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
		

		<?php
	}

	#echo the jquery to set the onclick methods.
	?>
	<script src="<?php echo $SITEURL.'/plugins/pajax/js/jquery.ba-hashchange.min.js' ?>"></script>
	<script type="text/javascript">
	jQuery(document).ready(function (){


		//#############################################
		// Ajax History
		//#############################################
		// Set the default to determine if this was just loaded and changed.
		window.pajaxLoaded = false;
		// Checks if its the first page.
		window.pajaxFirst = true;

		// Bind the event.
		$(window).hashchange( function(){
			
			// Get the hash and format it.
			var hash = location.hash;
			hash = hash.replace(/\|-\|/g, ' ');
			hash = hash.replace('#', '');
			if(hash == '')
				return;
			var first = jQuery('.current a');
			// Abort function if its the first load and the current page matches the hash.
			if(window.pajaxFirst && hash == jQuery('.current a').attr('title'))
				return false;

			// Abort function if the hash does not match any title.
			if($("<?php echo $nav ?>"+"[title=\""+hash+"\"]") < 1)
				return;

			// If the hash changed but there is none, assume it went back to the first one.
			if(hash.length == 0 && !window.pajaxLoaded && !window.pajaxFirst){
				// The title of the new page being loaded.
				var title = jQuery('.index a').attr("title");
				
				// The adress of the new page being loaded.
				var href = jQuery(".index a").attr("href");
				loadPages(jQuery('.index a'), href, title, false);
			}
			// If there is a hashtag and it was not loaded already
			else if(hash.length > 0 && !window.pajaxLoaded){
				
				// Go through each function in the navigation and check if its the one we need to load.
				jQuery("<?php echo $nav ?>").each(function(){
					var title = $(this).attr('title');
					if(title == hash){
						// The title of the new page being loaded.
						var title = jQuery(this).attr("title");
						
						// The adress of the new page being loaded.
						var href = jQuery(this).attr("href");
						loadPages($(this), href, title, false);
					}
				});
			}
			
			window.pajaxLoaded = false;
		});

		// Trigger the event (useful on page load).
		$(window).hashchange();

		//#############################################
		// On a navigation click
		//#############################################
		// binds the onclick to all of the files specified.
		jQuery("<?php echo $nav ?>").click( function(){
			
			// The title of the new page being loaded.
			var title = jQuery(this).attr("title");
			
			// The adress of the new page being loaded.
			var href = jQuery(this).attr("href");
			
			// Change the title to have a loading gif in it.
			var hLoading = 
			<?php 
			if($effect[0] == "none" || $effect[0] == ""){
				?>
				title;
				<?php
			}
			else if($gifPosition == "right"){
				?>
				title + " <img src='<?php echo $SITEURL.'/plugins/pajax/img/'.$lGif ?>' />";
				<?php
			}
			else{
				?>
				"<img src='<?php echo $SITEURL.'/plugins/pajax/img/'.$lGif ?>' /> "+title;
				<?php
			}
			?>
			jQuery("<?php echo $headline ?>").html(hLoading);

			loadPages($(this), href, title, true);
			
			return false;
		});
	});
	function loadPages(currentLink, href, t, setLoad){
			console.log("Page should be loading... "+href);
			//alert("happens");
			// Reset the current link
			$("<?php echo $nav ?>").parent().removeClass('current');
			currentLink.parent().addClass('current');
			<?php
			$animate = !($effect[0] == "none" || $effect[0] == "");


				// This happens if an effect has been set.
			if($animate){
					?>
					jQuery("<?php echo $content ?>").<?php echo $effect[0] ?>('slow', function(){
				<?php
				}
			?>

			
			// Loading gif in the content area, should only be see if no effect is set.
			var loading = 
			"<center>Loading "+t+"<br /><img src='<?php echo $SITEURL.'/plugins/pajax/img/'.$lGif ?>'</center>";

			// Set the loading animation
			jQuery("<?php echo $content ?>").html(loading);

			// Load the content. with a callback function that will remove all loading indicators and
			// call the final animation, if one is set.
			//jQuery("<?php echo $content ?>").load(href, function(){
				$.ajax({
					  	url: href,
						data: {pajax: 'RequestPage'},
						type: 'POST',
						success: function(data){
							<?php
								if($effect[0] != "none" && $effect[0] != ""){
									?>
									jQuery("<?php echo $content ?>").<?php echo $effect[1] ?>('slow');
									<?php
								}
							?>
							// If this was a click this will be true, if loaded from history false
							window.pajaxLoaded = setLoad;
							jQuery("<?php echo $content ?>").html(data);
							// If this was from history do not update hash.
							if(setLoad)
								window.location.hash=t.replace(/ /g, '|-|');

							// It is no longer the first call.
							window.pajaxFirst = false;

							// Update the title last to get rid of the gif.
							jQuery("<?php echo $headline ?>").html(t);
						},
						failure: function(){
							window.pajaxLoaded = setLoad;
							jQuery("<?php echo $content ?>").html("Error: The page could not be laoded. Please try again.");
							// If this was from history do not update hash.
							if(setLoad)
								window.location.hash=t.replace(/ /g, '|-|');

							// It is no longer the first call.
							window.pajaxFirst = false;

							// Update the title last to get rid of the gif.
							jQuery("<?php echo $headline ?>").html(t);

						}
				});



				<?php if($animate){
				?>
					});
				<?php
			}
			?>
	}
	</script>