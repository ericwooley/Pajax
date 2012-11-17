<?php
	global $SITEURL;
	# if the user saved settings
	pajaxAjaxSave();
	echo "<br /><br />";

	# reload the latest settings
	$settings = pajaxReadSettings();

	# If the "include Jquery" is set then set checked needs to be echoed
	if(strlen($settings[3]) > 1)
		$checked = "checked";
	else
		$checked = "";

	if(strlen($settings[6]) > 1)
		$disableSmoothCss = "checked";
	else
		$disableSmoothCss = "";

	# Echo the form with updated settings
	?>
	<style>
		.pajaxInput{width: 350px;}
	</style>
	<script>
		$(document).ready(function(){

			$("#pajaxAjaxSaving").change(function(){
				if($(this).is(':checked'))
					$("#pajaxSubmit").hide();
				else
					$("#pajaxSubmit").show();

			});
			
			$("#pajaxSettingsForm input, #pajaxSettingsForm select").change(function(){
				if($("#pajaxAjaxSaving").is(':checked')){
					//$(".pajaxSaving").remove();
					//alert('fired');
					$(this).after('<span class="pajaxSaving"> Saving... <img height=10 src="<?php echo $SITEURL.'/plugins/pajax/img/bar-spin-small.gif' ?>" /></span>');
					//alert($("#pajaxSettingsForm").serialize());
					$.ajax({
					  url: window.location,
					  data: $("#pajaxSettingsForm").serialize(),
					  success: function(data) {
					    $('.pajaxSaving').html(' Saved!');
					    $('.pajaxSaving').fadeOut('slow', function(){
					    	$(this).remove();
					    });
					  },
					  failure: function(data) {
					  	$('.pajaxSaving').html(' Could not save :(');
					  	alert("Data could not be saved :(");
					    $('.pajaxSaving').fadeOut('slow', function(){
					    	$(this).remove();
					    });
					  }
					});
				}
			});

			$("#autoInstall").submit(function(){
					if(!confirm("This feature is experimental, it is recommended that you backup your theme before you continue."
						)) return;
					$(this).after('<span class="pajaxSaving"> Modifying... <img height=10 src="<?php echo $SITEURL.'/plugins/pajax/img/bar-spin-small.gif' ?>" /></span>');
					$.ajax({
					  url: window.location,
					  data: $(this).serialize(),
					  success: function(data) {
					    $('.pajaxSaving').html("Modified!");
					    $("#pajaxModLog").prepend(data);
					    $('.pajaxSaving').fadeOut('slow', function(){
					    	//$(this).remove();
					    });
					    pajaxContentReset();
					  },
					  failure: function(data) {
					  	$('.pajaxSaving').html(' Could reach the server :(');
					  	alert("Data could not be saved :(");
					    $('.pajaxSaving').fadeOut('slow', function(){
					    	$(this).remove();
					    });
					  }
					});
				return false;
			});



			// Hides all the gifs.
			$("#pajaxHideShow").click(function(){
				$("#pajaxAnimations").slideToggle('slow');
				$(this).html('hide');
				return false;
			});
		});


		function pajaxContentReset(){
			if(!confirm("Theme has been successfully modified.\n If you press ok, the settings below will be modified to match the new themes settings, or cancel to leave them alone."))
				return;
			$("#content").val('.pajaxContent');
	    	$("#headline").val('.pajaxTitle');
	    	$("#linkDiv").val('.pajaxNav a');
	    	$('#linkDiv').trigger('change');
	    	pajaxSave();
		}

		function pajaxSave(){
			
					    
			if($("#pajaxAjaxSaving").is(':checked')){
					$.ajax({
					  url: window.location,
					  data: $("#pajaxSettingsForm").serialize(),
					  success: function(data) {
					  	//alert("Settings saved");
					  },
					  failure: function(data) {
					  	alert("Settings Not Saved");
					  }
					});
				}

		}
	</script>

	<h3>Pajax Auto Install (<u><b>experimental!</b></u>)</h3>
	<p>This feature will modify the selected theme so that it will probably work with pajax. It is experimental and there are some themes that it will not with.</p>
	<p>Some reasons a theme may not be compatable</p>
	<ol>
		<li>Javascript Errors in the theme. Many javascript errors break the functionality of the rest of the javascript. Since pajax is in javascript, a poorly written script in a theme may break it.</li>
		<li>The theme grabs content from get simple that is not usual and pajax does not anticipate it.</li>
		<li>While I have tried to make this work the best I can, there are a lot of ways to make web pages. It is hard to predict every case and I may have missed one. If you are having trouble with a theme, send me a link to it and I will try and add support to this feature. (5wooley4@gmail.com)</li>
	</ol>
	<i><u><b>Please keep in mind that I many themes may break, after modification. Particularly if they are poorly coded or don't follow get-simple guidelines. It is highligh recommended that you make a backup of your theme before trying this feature.</b></u></i>
	<?php 
		$themes = array_diff(scandir(GSTHEMESPATH), array('.','..'));
	?>
	<form action="" id="autoInstall" method="get">
		<select name='pajaxThemeInstall'>
			<?php
			foreach($themes as $theme)
				if(is_dir(GSTHEMESPATH.$theme)){
					?>
					<option><?php echo $theme ?></option>
					<?php
				}
			?>
		</select>
		<input type='submit' value='Modify Theme' />
	</form>
	<div>
		<legend>Modification log</legend>
		<fieldset id="pajaxModLog"></fieldset>
	</div>


		
	<form method='post' id="pajaxSettingsForm" action=''><br />

		<h3>Misc. Options</h3>

		<?php if(strlen($settings[8]) > 0) $pajaxAjax = "checked"; else $pajaxAjax = "";?>
		<p><input type="checkbox" name="pajaxAjax" id="pajaxAjaxSaving"  value="loadME" <?php echo $pajaxAjax ?> /> Enable ajax saving<br />
		This will save all changes as soon as you make them.</p>
		<br /><br />

		<label for="incJQ">Include a copy of jquery? (including multiple jquerys can cause issues in your javascript)</label>
		<input type='checkbox' value='Jq' name="incJQ" id="incJQ" <?php echo $checked ?>/> Include jQuery? <br />
		<br />

		<h3>-- Content Settings --</h3>
		<label for="linkDivs">Jquery object which contains all of your links.</label>
		<input type='text' class='pajaxInput' 
			placeholder="eg: #nav li" value='<?php echo $settings[0]; ?>' name="linkDiv" id="linkDiv" /> <br />
		<br />

		<label for="headline">Jquery object which contains the page title.</label>
		<input type='text' class='pajaxInput' 
			placeholder="eg: #header" value='<?php echo $settings[1]; ?>' name="headline" id="headline" /> <br />
		<br />

		<label for="linkDivs">Jquery object which the new content should be loaded into.</label>
		<input type='text' class='pajaxInput' 
			placeholder="eg: #content" value='<?php echo $settings[2]; ?>' name="content" id="content" /> <br />
		<br />

		<label for="effect">Effect to display before and after loading the new content.</label>
		<select class='pajaxInput' name="effect">
			<?php
				$effects = array("none", "fadeOut / fadeIn", "slideUp / slideDown");
				foreach($effects as $e){
					$sel = "";
					if($e == $settings[4] || ($e == "none" && $settings[4] == ""))
						$sel = "selected";
					?>
					<option <?php echo $sel ?>><?php echo $e ?></option>
					<?php
				}
			?>
		</select>
		<br />
		<input type="checkbox" name="disableSmoothCss" value="disableSmoothCss" <?php echo $disableSmoothCss ?>/>
		Do not include css to smooth animations (check this if it changes something you dont like.)
		<br /><br />
		

		<h3>-- Loading Animations -- <a href="#" id="pajaxHideShow">Show</a></h3>
		<div id="pajaxAnimations" style="display:none;">
		<p>If you want a custom gif, upload that gif into 'plugins/pajax/img/' and it will appear in the list.</p>
		Show animation to the <select name="animationPosition">
			<option 
				value="right"
				<?php if($settings[7] == "right") echo "selected"; ?>
			>
				right
			</option>
			<option 
				value="left"
				<?php if($settings[7] == "left") echo "selected"; ?>
			>
				left
			</option>
		</select> of the headline.
		<br /><br />
		<?php
			$dir = GSPLUGINPATH."pajax/img/";
			$gifs = array_diff(scandir($dir), array('.', '..', '.htaccess'));
			$i=0;
			foreach($gifs as $gif){
				$i++;
				if($gif == $settings[5] || ($settings[5] == "" && $i == 1))
					$sel = "checked";
				else
					$sel = "";
				?>
					<input type="radio" name="gif" value="<?php echo $gif ?>" <?php echo $sel ?> />
					<img src='<?php echo $SITEURL.'plugins/pajax/img/'.$gif ?>' />
					<p><?php echo str_replace(array('-', '.gif'), array(' ', ''), $gif); ?></p>
					<hr />
				<?php
			}
		?>
		</div>

		<input type='submit' 
				<?php if(strlen($settings[8]) > 0) echo 'style="display: none"';?>
				 id="pajaxSubmit" value='save settings' />
	</form>