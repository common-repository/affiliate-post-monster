<script type="text/javascript"> 
<!--
function SetAllCheckBoxes(FormName, FieldName, CheckValue)
{
	if(!document.forms[FormName])
		return;
	var objCheckBoxes = document.forms[FormName].elements[FieldName];
	if(!objCheckBoxes)
		return;
	var countCheckBoxes = objCheckBoxes.length;
	if(!countCheckBoxes)
		objCheckBoxes.checked = CheckValue;
	else
		// set the check value for all check boxes
		for(var i = 0; i < countCheckBoxes; i++)
			objCheckBoxes[i].checked = CheckValue;
}
// -->
</script> 
<?PHP
// feed setting page for the portaljumper.com plugin 2.8.2
include_once('pj_functions.php');
$x = plugin_dir_path(__FILE__);
//echo $x;
// error_reporting(0);
?>

			<style type="text/css">
			A:link { text-decoration: none } 
			.gallerycontainer{
			position: relative;
			/*Add a height attribute and set to largest image's height to prevent overlaying*/
			}
			.thumbnail img{
			border: 1px solid white;
			margin: 0 5px 5px 0;
			}
			.thumbnail:hover{
			background-color: transparent;
			}
			.thumbnail:hover img{
			border: 1px solid blue;
			}
			.thumbnail span{ /*CSS for enlarged image*/
			position: absolute;
			background-color: lightgreen;
			padding: 5px;
			left: -5px;
			border: 1px dashed gray;
			visibility: hidden;
			color: black;
			text-decoration: none;
			}
			.thumbnail span img{ /*CSS for enlarged image*/
			border-width: 0;
			padding: 2px;
			width:400px; 
			}
			.thumbnail:hover span{ /*CSS for enlarged image*/
			visibility: visible;
			top: 400px;
			left: 80px; /*position where enlarged image should offset horizontally */
			z-index: 350;
			}
			.round {
			width:98%;
			-moz-border-radius: 10px;
			border-radius: 10px;
			border: 4px solid #ccc;
			padding: 3px;
			}
			.titlebox{
			float:left;
			padding:0 5px;
			margin:-10px 0 0 30px;
			background:#ccc;
			color:white;
			font-weight:900;
			}
			</style>
<div style="text-align:center">
	<iframe src="http://portaljumper.com/wpplugin/plugintop2-news.php" width="100%" height="30" scrolling="no">Your system does not support Iframes so you can not see the latest news</iframe>


	<div class="round">
	<div class="titlebox">STEP 1.> network selection</div>
	
			<h2>Select your network, feed, and options</h2>

			<?PHP
			// kill cron (actually set postcounter to zero) when user clicks the stop cron button
			if (isset($_POST['pj-stopme']))
				{
				if ($_POST['pj-stopme'] == "stop") 
					{
					echo "<hr><div style='background:#ff0;color: red;'>CONFIRMATION: ALL JOBS CANCELLED</div><hr>"; update_option('pj-ppd','0');
					}
				}
			if (get_option('pj-ppd') == "0") 
				{ 
				echo "<hr><div style='background:#ff0;color: red;'><b>Processing of feeds was cancelled by you ! Please re-select a network and a new feed to restart the process.</b></div><hr>";
				}
			// check for cURL support
			if (_iscurlsupported()) 
				{
				echo "<div style='color:blue;font-size:11px'> (cURL is supported on your system) ... it's a good thing !
				</div><div style='font-size:10px'>Your monster is now able to dynamically update participating networks ! </div> ";
				} 
			else 
				{
				echo "<b><font color='red'>WARNING !! cURL is NOT supported on your system.<br><hr>YOU CAN NOT USE THIS PLUGIN WITHOUT cURL<hr>
				Please contact your host and have cURL installed or switch hosts (Since practically every host provides cURL support, your host obviously sucks). If you 
				are a do-it-your-self kinda person you can reinstall PHP with cURL support. Visit http://www.haxx.se/curl.html or better yet http://php.net/manual/en/book.curl.php for more info</font>";
				}
			$newfeed=0;

			// get available affiliate networks from pj
			$ch = curl_init();
			curl_setopt($ch,CURLOPT_URL,'http://linksalt.com/fmchome/networklist.php');
			curl_setopt($ch,CURLOPT_FRESH_CONNECT,TRUE);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,6);
			if(curl_exec($ch) === false) 
				{
				echo "<br><font color='red'>Curl error !<br>This needs to be fixed first before you can proceed !</font> " . curl_error($ch);
				}
			$data = curl_exec($ch);
			curl_close($ch);


			$networks = explode("|", $data);
			sort($networks);
			$netwcount = count($networks);
			?>
				<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=1; ?>"> 
				<select option name="pj-network">
				<option disabled>Select a network</option>
					<?PHP
					for ( $counter = 0; $counter < $netwcount; $counter++) 
					{echo "<option>".$networks[$counter]."</option>".$networks[$counter];}
					?>
				</select>
				<input type="submit" style="background-color:yellow" value="select network"/>
				</form>
	</div>
	
	<?PHP 
	if (isset($_POST['pj-network']))

		{
		?>
		</br>
		<div class="round">
		<div class="titlebox">Step 2.> FEED selection</div>
		<br>
		<?PHP
		// only run if something was posted 
			if ($_POST['pj-network'] != strip_tags($_POST['pj-network'])) {exit('fraud-alert');}
			$pj_network = str_replace ("'","",$_POST['pj-network']);	
			update_option('pj-network',$pj_network);

				// fetching datafeeds from selected network
				$url = "http://linksalt.com/fmchome/getfeeds.php?network=".get_option('pj-network');
				$fg = curl_init();
				curl_setopt($fg,CURLOPT_URL,$url);
				curl_setopt($fg,CURLOPT_FRESH_CONNECT,TRUE);
				curl_setopt($fg,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($fg,CURLOPT_CONNECTTIMEOUT,5);
				$feeds = curl_exec($fg);
				curl_close($fg);		
				
				// retrieve ID used for this particular network
				$selectID = get_option('pj-network');
				$finalID = get_option($selectID);
				// now let's go and check what kind of validuserid we have (freebie, PREMIUM, banned)
				
				$vurl = "http://linksalt.com/fmchome/checkid.php?checkid=".$finalID."&network=".$selectID;
				$vfg = curl_init();
				curl_setopt($vfg,CURLOPT_URL,$vurl);
				curl_setopt($vfg,CURLOPT_FRESH_CONNECT,TRUE);
				curl_setopt($vfg,CURLOPT_RETURNTRANSFER,1);
				curl_setopt($vfg,CURLOPT_CONNECTTIMEOUT,5);
				$validuser = curl_exec($vfg);
				$validuser = explode("|",$validuser);
				curl_close($vfg);
				update_option('validuserid',$validuser[0]);
				// end checking validuserid - the type of user is now stored				
				echo "Succesfully retrieved data for the <font color='red'>".get_option('pj-network')."</font> network | ";
				$feed = explode("|", $feeds);
				$feedcount = count($feed);
				echo "<font color='red'>".$feedcount."</font> active feeds were found. Please select your preferred feed below.";
				echo "<br>We are operating in <font color='red'>".$validuser[0]."</font> mode for this network.";
				if ($validuser[0] != "PREMIUM") {echo "<a href='http://portaljumper.com/feedmonster'>Upgrade ?</a><hr>";} 
				else 
				{echo "<hr>";}
				
				$newfeed = 0;
			
			
if (get_option('validuserid') == "PREMIUM"){?>
<script type="text/javascript"> 
<!-- 
//initial checkCount of zero 
var checkCount=0
var maxChecks=7
function setChecks(obj){ 
//increment/decrement checkCount 
if(obj.checked){ 
checkCount=checkCount+1 
}else{ 
checkCount=checkCount-1 
} 
//if a 4th box, uncheck the box, then decrement checkcount and pop alert 
if (checkCount>maxChecks){ 
obj.checked=false 
checkCount=checkCount-1 
alert('You may choose up to '+maxChecks+' feeds.\r\n Freebie users can only select 1 feed,\r\nPREMIUM users can select up to 7 feeds,\r\n ELITE members will have no limits. !') 
} 
} 
//--> 
</script>
<?PHP
}else{
?>
<script type="text/javascript"> 
<!-- 
//initial checkCount of zero 
var checkCount=0
var maxChecks=1
function setChecks(obj){ 
//increment/decrement checkCount 
if(obj.checked){ 
checkCount=checkCount+1 
}else{ 
checkCount=checkCount-1 
} 
//if a 4th box, uncheck the box, then decrement checkcount and pop alert 
if (checkCount>maxChecks){ 
obj.checked=false 
checkCount=checkCount-1 
alert('You may choose up to '+maxChecks+' feeds.\r\n Freebie users can only select 1 feed,\r\nPREMIUM users can select up to 7 feeds,\r\n ELITE members will have no limits. !') 
} 
} 
//--> 
</script>
<?PHP
}

			
			if (get_option('validuserid') == "PREMIUM") {$maxsel = 7;
			}
			elseif (get_option('validuserid') == "ELITE") {$maxsel = 999;
			}
			else {$maxsel = 1;
			}
			?>							
								
			<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=2; ?>" name="feedselect"> 
			<?PHP if (get_option('validuserid') != "ELITE") {echo '<input type="button" value="select all!">';} 	
			else {echo '<input type="button" onclick="SetAllCheckBoxes(\'feedselect\', \'pj-feed[]\', true);" value="select all!">'; }	?>		
			(ELITE members only)
			<input type="button" onclick="SetAllCheckBoxes('feedselect', 'pj-feed[]', false);" value="clear all!">
			<br>Premium members can select up to 7 feeds. Freebie members can only select 1 feed<br>
			<div style="width:90%;height:250px;overflow:auto">
				<?PHP
					$Q=0;
					for ( $feedcounter = 0; $feedcounter < $feedcount; $feedcounter++) 
						{
						$Q++;
						echo "<input type='checkbox' name='pj-feed[]' value='$feed[$feedcounter]' onclick='setChecks(this)';> $feed[$feedcounter]<br>";
						}
				?>
			</div>
			<div style="text-align:center">
			<a class="thumbnail" href="#thumb">Tip !<span>
			Use the searbox in your browser to search through all the datafeed text.<br>
			For instance on google chrome:<br>
			Click the tool icon on the right side of your menu bar & click "search"<br>
			<b>OR</b><br>
			simply hit CTRL + F (Works on most browsers).<br>
			Now you can search for any part of the datafeed title and have it automatically highlighted.
			<hr>
			READING THE DATAFEED NAME<hr>
			The new datafeed format will show feed titles as follows:<br>
			MERCHANT NAME _ MERCHANT ID _ AMOUNT OF PRODUCTS IN FEED .txt<br>
			You can find merchants in alphabetical order, or perform a search on the ID number.<br>
			Take note of the product count to estimate how many products will be pushed into your system.
			</span></a><br>
			<label for=net style="font-weight:600">Please select a network that you are affiliated with from the list above.<br>then click the button below</label><br>
			<input id=net style="background-color:yellow" type="submit" value="select the datafeed to work with"/>
			</form>
			</div>
			<div style="clear:both;"></div>
		</div> 
			<?PHP	
		// end // only run if something was posted 
		}
		
		
		
		
	// found network and feeds commencing
	if (!empty($_POST['pj-feed']))
		{		
		?>																		
		</br>
		<div class="round">
		<div class="titlebox">Step 3.> select your OPTIONS</div>		
		<br>Feeds selected : 
		<?PHP $allfeeds = array(); foreach ($_POST['pj-feed'] as $feed) {echo "|> $feed <|";$allfeeds[] = $feed;} ?>
		
		<h4>Select which options you want to apply !</h4>
		<table width="90%">
		<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=3; ?>" name="option_select"> 
		<td width="33%">
		<b>layout of the posts is:</b><br>
		<input type="radio" name="pj-layout" value="A" checked> top to bottom
		<a class="thumbnail" href="#thumb">example<span><img src="http://portaljumper.com/wpplugin/pics/example1.jpg"></span></a><br>
		<input type="radio" name="pj-layout" value="B"> left to right
		<a class="thumbnail" href="#thumb">example<span><img src="http://portaljumper.com/wpplugin/pics/example2.jpg"></span></a><br>
		<input type="radio" name="pj-layout" value="E"> text only
		<a class="thumbnail" href="#thumb">example<span><img src="http://portaljumper.com/wpplugin/pics/example3.jpg"></span></a><br>
		<input type="radio" name="pj-layout" value="C" <?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. You can do so on the bottom of this screen.')" <?PHP ;} ?>> linked images only
		<a class="thumbnail" href="#thumb">example<span><img src="http://portaljumper.com/wpplugin/pics/example4.jpg"></span></a><br>
		</td>

		<td>
		<b>Allow comments on posts :</b><br>
		<input type="radio" name="pj-commentstatus" value="open" checked> allowed | 
		<input type="radio" name="pj-commentstatus" value="closed" <?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. You can do so on the bottom of this screen.')" <?PHP ;} ?>> not allowed<br>
		<b>set all posts to status :</b><br>
		<input type="radio" name="pj-poststatus" value="publish" checked> automatically published<br>
		<input type="radio" name="pj-poststatus" value="draft"> draft<br>
		<input type="radio" name="pj-poststatus" value="pending"> pending(waiting for review)<br>
		<input type="radio" name="pj-poststatus" value="private"> private<br>
		</td>

		<td>
		<b>I want to (drip)feed posts at a rate of approx.:</b><br>
		<input type="radio" name="pj-ppd" value="24" checked> 24 posts a day<br>
		<input type="radio" name="pj-ppd" value="96" <?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. You can do so on the bottom of this screen.')"<?PHP ;} ?>> 96 posts a day<br>
		<input type="radio" name="pj-ppd" value="240" <?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. Premium members earn more money through affiliate marketing.')"<?PHP ;} ?>> 240 posts a day 
		<br>
		<input type="radio" name="pj-ppd" value="624"<?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. PREMIUM status will remove all advertisements.')" <?PHP ;} ?>> 624 posts a day <br>
		<input type="radio" name="pj-ppd" value="1248"<?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in the freebie version. Please consider upgrading to PREMIUM membership. You can do so on the bottom of this screen.')"<?PHP ;} ?>> 1.248 posts a day *<br>
		<input type="radio" name="pj-ppd" value="2880"<?PHP if (get_option('validuserid','an unknown') != "PREMIUM") {?> onClick="this.checked=false; alert('Sorry, this option is not available in your version. Please consider upgrading to ELITE membership. Contact the developer.')"<?PHP ;} ?>> ELITE Blaster *<br>
		
		</td>
		</table>

		<!-- start the cats -->
	</br>
		<div class="round">
		<div class="titlebox">Step 3-A.> PREMIUM member only, options</div>	
			<br>		
			( all items below are optional:)<br>
				<div>			
				<br>
				</div>
			
			<div>
			Add extra cats:<input type="text" size="60" name="pj-ecats" value="<?PHP echo get_option('pj-ecats','') ; ?>">
			Don't include standard category names -> <input type="checkbox" name="pj-standcats" <?PHP if (get_option('pj-standcats') == "on") echo"checked"; ?> >
			<a class="thumbnail" href="#thumb">help<span>
			This option enables you to put the ENTIRE FEED SELECTION in a category of your choice.<br>Instead of taking the categories from the feed, you could for instance put an entire
			 toystore feed into 1 category called "toys". To do this simply enter ",toys" in the box, and check the "Don't use standard categories" box.<br>
			You can also add extra category names here if needed. Enter your categories separated 
			by a comma, for example : <br><br>
			<b>garden,outdoor,air</b><hr><br>
			please note that your products will also appear in the categories that the merchant has put in the datafeed.<br>
			If you do not want to use the categories that came with the datafeed you can check the appropriate<br>
			box behind the categorie bar. If you check the box and you do not enter categories yourself<br>
			then all posts will be placed in "uncategorized"<br>
			Please note that these categories will be added to every post until you remove them here.<br>
			In other words: these categories will be used for every post in this feed.
			</span></a>
			</div>

			<!-- start the tags -->
			<div>
			Add extra tags: <input type="text" size="120" name="pj-etags" value="<?PHP echo get_option('pj-etags','') ; ?>">
			<a class="thumbnail" href="#thumb">help<span>
			You can add extra tags here if needed. Enter your tags separated 
			by a comma, for example : <br><br>
			<b>garden,outdoor,air</b><hr><br>
			please note that your products will also be tagged with information that the merchant has put in the datafeed.<br>
			You can add extra tags as a way to "label" your feeds too so you can easily select them for deletion or manipulation.<br>
			Please note that these tags will be added to every post until you remove them here.<br>
			In other words: these tags will be used for every post in this feed.
			</span></a>
			</div>

			<!-- start the disclaimer box -->
			<div>
			Write a note or disclaimer: <textarea cols="50" placeholder="enter an important message underneath every post" rows="4" name="pj-note" ><?PHP if (get_option('pj-note','') == "") echo "Enter your disclaimer or any other recurring text in this box."; else echo get_option('pj-note','') ; ?></textarea>
			<a class="thumbnail" href="#thumb">help<span>
			In the text area you can enter a note or important information (like for instance a disclaimer etc.) <br>
			<hr>
			This note will show up as a box on the bottom left side<br>
			underneath your posts.<br>
			HTML code IS allowed, but make sure you know what you are doing !<br>
			This plugin does not protect your database info from malicious or broken code you may put in this box !
			<hr>
			p.s. Anything you put in this box is wrapped in "< small >" tags. (showing small print on the final post).<br><hr>
			tips:<br><font color='blue'> Don't drink and drive<br>
			When using powertools you must take precautions.<br>
			Fur Nebenwirkungen >> fragen Sie bitte Ihren Arzt oder Apotheker<br>
			or add light html like < a href = http : // google.com > google < / a > (spaced on purpose to show text).
			</font>
			</span></a>
			</div>

			<!-- start the keyword-swap check -->
					<div style="margin-left:50px;">

					<a class="thumbnail" href="#thumb"><font color='red'><b>SEO Optimizer :</b> automatically replace words by other words. MORE ...</font>
					<span>
					<font color='red'><b>SEO Optimizer :</b> automatically replace words by other words.</font>
					<hr><br>
					This is a PREMIUM members only feature !<br><hr>
					Search engines just HATE duplicate posts and they may punish you with lower ratings <br>
					if they find that your site is simply showing posts that it can find everywhere on the internet.<br>
					<b>Behold the keyword-swap power</b><br>
					In the boxes below you can enter up to 10 keywords that relate to your niche-products, and replace them<br>
					with synonyms. Search engines will now read your posts and find unique periodically added content.<br>
					We now have a close human analogue, and search engines will like your sites a lot better.
					<hr>
					TIP: You can use this tool to change the text before the price field too :<br>
					Priced at only $ > Jetzt nur $  .....or .....<br>
					Priced at only $ > limited time only, now just $
						<?PHP 
						if (get_option('validuserid','an unknown') == "freebie") {echo "<br>Freebie mode detected ! The SEO optimizer can be filled out BUT IT WILL NOT WORK in freebie mode !";} 
						?>
					</span></a>	 
					<?PHP 
					//create keyword swap list
					for ($i = 1; $i <= 10; $i++) 
						{
						?>
						<br>turn this> <input type="text" size= "15" name="pj-before[]" value="<?PHP echo get_option("pj-before$i"); ?>" > 
						into this> <input type="text" size= "15" name="pj-after[]" value="<?PHP echo get_option('pj-after'.$i); ?>" > 
						<?PHP
						}
					?>
					<br>
					<?PHP 
					if (get_option('validuserid','an unknown') == "freebie") 
						{
						echo "<b><br>Freebie mode detected ! The SEO optimizer can be filled out<br>BUT IT WILL NOT WORK in freebie mode !</b>";
						} 
					?>
					</div>
		</br>
		<div class="round">
		<div class="titlebox">Step 3-B.> ELITE member POWER OPTION</div>		
		<br>
		Only process posts when this keyword is in the text > : <?PHP if (get_option('validuserid') == "PREMIUM") {echo '<input type="text" size="50" name="pj-keyword">';} else {echo " --sorry PREMIUM only-- ";} ?> (ELITE pack only)<br>. Choose wisely ! The system will only return posts that have the keyword as part of the description. If searching for "car" you will get a match on CAR, but also on CARhorn or sCARred !!
		
		</div>
	
		</div>		
	</div>
<br>
<div class="round">
<div class="titlebox">FINAL Step .> Click to preview post and start loading.</div>
	<br>
	<input type="hidden" name="3done" value="1">
	Click here when you are done ! >>>> <input type="submit" style="background-color:yellow" value="use these options on my posts"/>  <<<< Click here when you are done !
	</form>
</div>	
		<?PHP
		// end showing option forms if a feed exists
		}
	if (isset($_POST['pj-ecats'])) {if (substr($_POST['pj-ecats'], 0, 1) != ",") $_POST['pj-ecats'] = ",".$_POST['pj-ecats']; }
	if (isset($_POST['pj-ecats'])) {update_option('pj-ecats',$_POST['pj-ecats']);}
	if (isset($_POST['pj-etags'])) {if (substr($_POST['pj-etags'], 0, 1) != ",") $_POST['pj-etags'] = ",".$_POST['pj-etags']; }
	if (isset($_POST['pj-etags'])) {update_option('pj-etags',$_POST['pj-etags']);}
	if (isset($_POST['pj-standcats'])) {update_option('pj-standcats','on');} else {update_option('pj-standcats','off');}
	if (isset($_POST['pj-network'])) {update_option('pj-network',$_POST['pj-network']);}
	if (isset($_POST['pj-feed'])) {update_option('pj-feed',maybe_serialize($_POST['pj-feed'])); $selected_feeds = maybe_unserialize(get_option('pj-feed')); update_option('pj-selected-feed',$selected_feeds[0]); update_option('pj-selected-feed-counter','0');}	
	if (isset($_POST['pj-keyword'])) {update_option('pj-keyword',$_POST['pj-keyword']); }
	if (isset($_POST['pj-layout'])) {update_option('pj-layout',$_POST['pj-layout']);}
	if (isset($_POST['pj-tot'])) {update_option('pj-tot',$_POST['pj-tot']);}
	if (isset($_POST['pj-poststatus'])) {update_option('pj-poststatus',$_POST['pj-poststatus']);}
	if (isset($_POST['pj-commentstatus'])) {update_option('pj-commentstatus',$_POST['pj-commentstatus']);}
	if (isset($_POST['pj-ppd'])) {update_option('pj-ppd',$_POST['pj-ppd']);}
	if (isset($_POST['pj-note'])) {if ($_POST['pj-note'] == "Enter your disclaimer or any other recurring text in this box.") update_option('pj-note',""); else update_option('pj-note',$_POST['pj-note']);}
	
	// setting keyword swap options
	$i=0;
		if (isset($_POST['pj-before'])) 
			{
			foreach ($_POST['pj-before'] as $key => $value) 
				{
				$i++;
				update_option("pj-before$i",$value);
				}
			}

	// load the after keywords into array
	$i=0;
		if (isset($_POST['pj-after'])) 
			{
			foreach ($_POST['pj-after'] as $key => $value) 
				{
				$i++;
				update_option("pj-after$i",$value);
				}
			}
	?>
	</br>
	<div class="round">
	<div class="titlebox">Preview and information screen (final confirmation below).</div>
	<br>
	<?PHP
	$selectID = get_option('pj-network');
	$finalID = get_option($selectID);
	if (empty($finalID)) 
		{
		$finalID = "<div style='background:#ff0;color: red;'><strong>WARNING -- I DO NOT HAVE YOUR ID FOR THIS NETWORK <br><a href='admin.php?page=pj-admin'> Click here to go set your ID now ! </a>, or select a different network !</strong></div>";
		}
	$selected_feeds = maybe_unserialize(get_option('pj-feed'));
	echo "Selected network : <font color='red'>".get_option('pj-network')."</font> with identification : <font color='red'>".$finalID."</font><br>";
	$howmany = count($selected_feeds);
	$post_per_day = get_option('pj-ppd');
	if (!empty($post_per_day)){$gap = 86400 / $post_per_day ;	
	}
	else{ $gap = 120;
	}
	echo "You have selected $howmany datafeed(s) ! showing up to first three : <font color='red'> $selected_feeds[0] - $selected_feeds[1] - $selected_feeds[2] </font> <br>Layout: <font color='red'>".get_option('pj-layout')."</font> | Dripfeeding posts to your database every <font color='red'>".substr($gap/60,0,4)." minute(s)</font> (or $post_per_day posts per day). | Proposed search word <font color='red'>".get_option('pj-keyword')."</font>";
	if (get_option('pj_postcount') == "") 
		{
		update_option('pj_postcount',0);
		}
		
	if ($_POST['3done'] == 1) 
		{
		update_option('pj-mail','notsent'); 
		
		// all options selected, contacting mainframe to transmit posts
		// preview routine -----build on server -----------------------
		echo "<div style='background-color:#FAF8CC;'>";
		$whopr = get_bloginfo('url')."-preview";
		//$furl = "http://linksalt.com/fmchome/shake.php?pr=1&who=".$whopr."&id=".$finalID."&tot=".get_option('pj-tot')."&n=".get_option('pj-network')."&f=".get_option('pj-selected-feed')."&l=".get_option('pj-layout')."&amail=".get_option('admin_email')."&p=".get_option('pj-ppd');	
		$furl = "http://linksalt.com/fmchome/shake.php?pr=1&who=".$whopr."&id=$finalID&sc=$postcounter&tot=".get_option('pj-tot')."&n=".get_option('pj-network')."&f=".get_option('pj-selected-feed')."&l=".get_option('pj-layout')."&p=".get_option('pj-ppd')."&who=$who&search=$pj_searchword&amail=".get_option('admin_email')."";				
		$furl = str_replace(" ", "%20", $furl);
		
		$exg = curl_init();
		curl_setopt($exg,CURLOPT_URL,$furl);
		curl_setopt($exg,CURLOPT_FRESH_CONNECT,TRUE);
		curl_setopt($exg,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($exg,CURLOPT_CONNECTTIMEOUT,5);
		$example = curl_exec($exg);
		curl_close($exg);
		echo $example;
		
		if (get_option('pj-note') != "") 
			{ 
			echo "<div style='text-align:left;width:100%;'><div style='text-align:left;width:250px;background-color:lightyellow'><small>".get_option('pj-note')."</small></div></div>" ;
			}	
		$totalfeedlines = inbetween($example,'I found:','products in this feed');
		update_option('pj_totalfeedlines',$totalfeedlines);
		//print_r(get_option('pjfinalafter'));
		echo "<br>Comments on these posts are set to: <font color='blue'>".get_option('pj-commentstatus','unknown')."</font>";
		echo "<br>Post-status once loaded is set to: <font color='blue'>".get_option('pj-poststatus','unknown')."</font>";
		echo "<br>Additional categories added: <font color='blue'>".get_option('pj-ecats','none')."</font>"; 
		if (get_option('pj-standcats') == "on") echo "<br><b> You chose to suppress standard categories!</b>";
		echo "<br>Additional tags added: <font color='blue'>".get_option('pj-etags','none')."</font>";			 
		?>
		<hr width="40%">
		<p align="center">The yellow highlighted box above shows a quick preview of a post as you selected it.<br>If this preview is acceptable hit proceed below to start dripfeeding posts from this feed into your database, else start the selection process again.</p>
		</div>
		<br>
			<?PHP
				$petesblock = explode("|",$example);
				if ($petesblock[0] == "blocked") die ('<center><b>Feed-monster is tired !<br>going into sleep mode now. I will wake up by myself. goodnight.....</b>');
			?>
		<div style='background-color:#FAF8CC;text-align:center;'>
		<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=4; ?>"> 
		<input type="hidden" name="resetcounter" value="1">
		<input type="hidden" name="prvok" value="1">
		<input type="submit" value="I AM OK WITH THE PREVIEW - start dripfeeding these posts" style="background-color:yellow"/>
		</form>
		</div>
		<?PHP
		}
		
		
		
	if ($_POST['resetcounter'] == 1) 
		{
		echo "<br>post counter was reset"; update_option('postcounter', 0); 
		}
	if ($_POST['prvok'] == 1)
	// preview post is ok - starting the real work
		{
		if (empty($postcounter)) 
			{
			$postcounter = 0;
			}
		?>
		<div style='background-color:#C3FDB8;text-align:center;'>
		<hr>
		<?PHP
		$timerstart = get_option('pj_timer');
		if ($timerstart > time() + 60 ) 
			{
			$timerstart = time() + 10; update_option('pj_timer',$timerstart);
			echo "<br> I have reset the job-counter for you to speed things up a bit<br>(I do this every time when you hit the green button and when the job timer still holds 60 seconds or more)<br>Wait about 10 seconds and hit the green button again to see the job run.<br>";
			}
		$timestamp = $timerstart;
		$diff = $timestamp - time(); $diff = $diff/60;
		?>
		<b>Scheduled jobs - (next update will run in <?PHP  echo substr($diff,0,4) ; ?> minutes !)</b>
		<hr>
		Current job:<br>
		<?PHP 
		echo "<font color='blue'>".get_option('pj-selected-feed')."</font>"; 
		echo " - ".date('l jS \of F Y h:i:s A')."<small> (servertime)</small>";
		
		
		$post_per_day = get_option('pj-ppd');
		if (!empty($post_per_day)) {$gap = 86400 / $post_per_day ;	
		}
		else
		{$gap = 120;
		}
		
		echo "<br>I am waiting <font color='blue'>". substr($gap/60,0,4) . " minutes</font> each time, <b>and will then try to load another post as soon as the next visitor stops by !</b><br>This feed has a total of <font color='blue'>".get_option('pj_totalfeedlines','approximately 10 quadrillion ')."</font> articles.<br>";
		$correctioncount =  get_option('pj-selected-feed-counter'); $correctioncount = (int)$correctioncount + 1;
		echo "You have selected <font color='blue'>$howmany</font> feeds. We are now processing feed <font color='blue'>$correctioncount / $howmany</font><br><br>";
		echo "So far I have gone through <font color='blue'>".get_option('postcounter','an unknown amount of')."</font> cycles for this particular monster-job.<br>
		Not every cycle may produce a product or post in your wordpress. <br>Feed monster has filters for duplicate content,searchwords, and looks for broken,bad or dangerous feed items.<br>
		If feed monster detects a filter-event it will skip that post,<br>but it will still add to the cycle counter.";
		if (get_option('pj-note') != "") { echo "<div style='text-align:left;width:100%;'><div style='text-align:left;width:250px;background-color:lightyellow'><small>".get_option('pj-note')."</small></div></div>" ;}	
		echo "<br><br>Comments on these posts are set to: <font color='blue'>".get_option('pj-commentstatus','unknown')."</font>";
		echo "<br>Post-status once loaded is set to: <font color='blue'>".get_option('pj-poststatus','unknown')."</font>";
		echo "<br>Additional categories added: <font color='blue'>".get_option('pj-ecats','none')."</font>"; 
		if (get_option('pj-standcats') == "on") echo "<br><b> You chose to suppress standard categories!</b>";
		echo "<br>Additional tags added: <font color='blue'>".get_option('pj-etags','none')."</font>";

		echo "<br><br>This is now a FIRE and FORGET thing. The system is set on automatic. Relax ... check back later and see your posts appear automagically";
		echo "<hr><b>F.A.Q.</b> - Why do I see a negative number on the next update ?<br>The negative number means that not a single person has visited your blog since the last update moment. To run the next update simply
		visit the front page of your blog, or click the button below (run cron on negative time) and the update will run. TIP : try to get more visitors :-)<hr>";

		$A = get_option('postcounter','0');
		$A = (int)$A;
		$B = get_option('pj_totalfeedlines','9999999');
		$B = (int)$B;
		echo "<br> So far I have worked on $A posts. This particular feed has $B posts";
		if ( $A >= $B) 
			{
			exit ('<br><font color="red"><b>I am done loading the entire feed. Please select another datafeed to work on</b><br><a href="http://portaljumper.com/feedmonster" target="_blank">Like this plugin and not a premium member yet ? Upgrade now !</a></font><br><hr>');
			}
		ticktock("on");
		
		}
	?>
	</div>
	</div>
	<div style="text-align:center">
	<br>
	Hit the green button below to see the currently running job and get some statistics.<br> You can also click this button to force a loading cycle (provided the cron time is negative)<br>
	<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=5; ?>"> 
	<input type="hidden" name="prvok" value="1">
	<input type="submit" value="show job & run cron on negative time" style="background-color:#81F781" title="show progress on currently running job. Hitting this button will also wake up the monster ! (if scheduled time is negative)"/>
	</form>
	</br>
	Hitting the red button below will cancel all automated feed-monster activity<br>
	<form action="admin.php?page=set_feeds" method="post" onSubmit="<?PHP $newfeed=6; ?>"> 
	<input type="hidden" name="pj-stopme" value="stop">
	<input type="submit" value="cancel all running jobs" style="background-color:#FA5858" title="WARNING .. this will instantly kill the current job"/>
	</form>

	</div>
	<p align="center">Portaljumper.com's - feed-monster<br>Program & Design by: Pete Scheepens</p>
	<div style="color:#95B9C7;font-size:10px">* Restrictions apply on these settings & options for freebie users. Due to high demand the portaljumper serverfarms are under heavy load 
	and we must restrict certain premium services to premium users only. When server loads are light we may choose to allow non-paying customers access to premium services. In such instances we will not directly communicate availability of these services. Paying members are always first in line for bandwidth and premium services.
	PREMIUM benefits include (but are not limited to) SEO tools, no link-sharing, no advertisements, no branding, high server capacity, more datafeeds.<a href="http://portaljumper.com/feedmonster">go premium today</a>.</div>

	<?PHP 
	if (file_exists($x.'pj_footer.php')) include ($x.'pj_footer.php'); 
	?>	
</div>






















