<style type="text/css">			
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

<?PHP
// portaljumper.com feed-monster - pagebuilder by pete scheepens

include_once('pj_functions.php');
//$x = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
$x = plugin_dir_path(__FILE__);
echo $x;

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

<iframe src="http://portaljumper.com/wpplugin/plugintop2-news.php" width="100%" height="30" scrolling="no">Your system does not support Iframes so you can not see the latest news</iframe>
<br>
	<div class="round">
	<div class="titlebox">Product Page builder</div>
	<br>
		<div style="padding-left:10px;text-align:center">
		<h4>feed-monster.com's pagebuilder </h4>
		The tool on this page can build wordpress pages that display many of your products at once. More products on a page means more chances to sell.<br>
		Please note that this is primarily a PREMIUM member's tool. if you use it in freebie mode it will work, but you will share a good portion of your product links. We highly recommend upgrading to PREMIUM when using this tool.
		<hr>
		<form action="admin.php?page=build_page" method="post">
		<input type="submit" name="video" value="show a help video" style="background-color:#81F781">
		</form>
		<?PHP 
		if ($_POST['video'] == "show a help video")
			{ echo '<iframe title="YouTube video player" width="480" height="390" src="http://www.youtube.com/embed/8JfymTV4PQs" frameborder="0" allowfullscreen></iframe>
			<form action="admin.php?page=build_page" method="post">
			<input type="submit" name="video" value="hide this video" style="background-color:#81F781">
			</form>
			';
			}
		?>
		<br>
		</div>
	</div>

<?PHP

// reply on screen when page is complete
if (isset($_POST['select1'])) 
		{
		?>
		<br>
		<div class="round">
		<div class="titlebox">Page is done - check it out</div>
		<br>
		<?PHP
		if (empty($_POST['shoptitle']))	{$_POST['shoptitle'] = "feed-monster.com shops";}
	
		echo "<h2>CHECK IT OUT ! A new page named <font color='blue'>".$_POST['shoptitle']."</font> was created ! </h2>";
		
		$search= $_POST['pj-keyw'];
		if (empty($search)) {$key = "not provided";$s = "";} else {$key = $search;$s = "search=$search";}
		
		echo "<b>The seedling keyword (if any) for this shop is </b><font color='red'><b>".$key."</b></font><br>";		
		
		$content = "[pjcode $s net=".$_POST['pj-pagenet']." layout=".$_POST['pj-layout']." feed=".$_POST['pj-pagefeed']." count=".$_POST['pj-count']." ident=".$_POST['ID']."]";
		
		// Create post object
			$my_post = array(
			'post_type' => 'page',
			'post_title' => $_POST['shoptitle'],
			'post_content' => $content,
			'comment_status' => $_POST['comment'],
			'post_status' => 'publish',
			'post_author' => 1,  );
		// Insert the post into the database
			$pageID = wp_insert_post( $my_post );
			echo "<br><b>Note that the actual URl to your new page depends on your permalink settings.</b><br>Your new page can be found here : ";
$permalink = get_permalink( $pageID ); echo "<a href='$permalink' target='_BLANK'>$permalink</a>";
		?></div><?PHP
		}

// end on-screen reply
// start network form first
?>
<br>
<div class="round">
<div class="titlebox">PAGE CREATOR </div>
<br>
	<div style="background-color:#CEF6CE;width:100%;text-align:center">
	<h2>All right ! Let's start building a product-page ....</h2>Select a network from the pulldown box to get started !<br>
		<form method="post"> 
		<!-- select a network -->
		<select option name="pj-pagenet">
		<option>Select a network</option>
			<?PHP
			for ( $counter = 0; $counter < $netwcount; $counter++) 
			{echo "<option>".$networks[$counter]."</option>".$networks[$counter];}		
			?>
		</select>
		<input type="hidden" name="netselect" value="yes">
		<input type="submit" style="background-color:yellow" value="select this network"/>
		</form>

<?PHP

// once network selected get other options	
if ($_POST['netselect'] == "yes")
	{
			// fetching datafeeds from selected network
			if ($_POST['pj-pagenet'] != strip_tags($_POST['pj-pagenet'])) {exit('fraud-alert');}
			$pagenet = str_replace ("'","",$_POST['pj-pagenet']);
			// fetching datafeeds from selected network
			$url = "http://linksalt.com/fmchome/getfeeds.php?network=$pagenet";
			$fg = curl_init();
			curl_setopt($fg,CURLOPT_URL,$url);
			curl_setopt($fg,CURLOPT_FRESH_CONNECT,TRUE);
			curl_setopt($fg,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($fg,CURLOPT_CONNECTTIMEOUT,5);
			$feeds = curl_exec($fg);
			curl_close($fg);
			// explode & count stuff
			$feed = explode("|", $feeds);
			$feedcount = count($feed);
			// retrieve ID used for this particular network
			$network = $_POST['pj-pagenet'];
			$selectID = get_option($network); 
					
			// now let's go and check what kind of validuserid we have (freebie, PREMIUM, banned)
			$vurl = "http://linksalt.com/fmchome/checkid.php?checkid=".$selectID."&network=".$_POST['pj-pagenet'];
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
			echo "<hr>";
			echo "Succesfully retrieved data for the <font color='red'>".$_POST['pj-pagenet']."</font> network | ";
			
			echo "<font color='red'>".$feedcount."</font> feeds were found.";
			if (empty($selectID)) exit("<br><h3><font color='red'>Sorry, but I can not find an ID for the ".$_POST['pj-pagenet']." network. Please select another network, or go enter your ID first !</font></h3>");
			echo "<br>your ID: <font color='red'> $selectID</font> is registered with this network in <font color='red'>".get_option('validuserid')."</font> mode.<br>Please proceed with your selection below.<hr>";
			$newfeed = 0;
			// end fetching datafeeds from selected network
			?>
			<form method="post"> 
			<!-- select a title -->
			<br>
			Your new shop acts as a PAGE in wordpress.<br>
			Please provide a title for your new shop -><br>
							<script>			
								function clearText(thefield){
								if (thefield.defaultValue==thefield.value)
								thefield.value = ""
								} 
							</script>							
			<input type="text" name="shoptitle" size="60" value="feed-monster.com shops ..." onFocus="clearText(this)"><br><br>
			
			<div style="margin:auto;width:70%;height:150px;overflow:auto;text-align:left;background-color:#F8ECE0">
			<?PHP
				for ( $feedcounter = 0; $feedcounter < $feedcount; $feedcounter++) 
					{
					echo "<input type='radio' name='pj-pagefeed' value='$feed[$feedcounter]'";
					 if ($feedcounter == "3") echo "CHECKED";
					echo ">$feed[$feedcounter]<br>";
					}
			?>
			</div>
			<br><br>
			<!-- select amount og products -->
			how many products do you want to show on a page<br>
			<?PHP
				for ( $counter = 4; $counter < 100; $counter=$counter + 4) 
				{
				$counterplus = $counter + 1;
				echo "<input type='radio' name='pj-count' value='$counterplus' ";
				if ($counter == "20") echo "CHECKED";
				echo ">".$counter."|";
				}
				?>			
			<br><br>
			<!-- select comment status -->
			Would you like to enable comments on your shop ?<br>
			<input type="radio" name="comment" value="open"> yes 
			<input type="radio" name="comment" value="closed" checked> No<br><br>			
			If you want your shop to start with only certain items provide a keyword here, otherwise leave blank.
			<br> (If you do not have products with this word in the title your shop will be empty).<br>
			<input type="text" name="pj-keyw" /><br>
			
			<br><br>
			Finally select a layout below:<hr>
			
			<table width="90%" style="text-align:center">
			<tr>
			<td width="200">
			<div style="float:left;text-align:center;width:200px;height:350px;overflow:hidden">
			select : <input type="radio" name="pj-layout" value="1" checked> <br>
			<img src="http://linksalt.com/fmchome/layoutpics/layout1.jpg" width="140"><br>
			<br>(DEFAULT LAYOUT)<br>300 x 200 pixel Bright yellow bars; images with embedded link and hover text and title below image. Image, title and price are all affiliate links.			
			</div></td>
			
			<td width="200">
			<div style="float:left;text-align:center;width:200px;height:350px;overflow:hidden">
			select : <input type="radio" name="pj-layout" value="2"> <br>
			<img src="http://linksalt.com/fmchome/layoutpics/layout2.jpg" width="140"><br>
			<br>A 300 x 200 pixel businesscard type layout with 3 links embedded in each card. Cards are placed side-by-side. Good for wide pages (columns of 600+ px or 900+ px wide)			
			</div>
			</td>
			
			<td width="200">
			<div style="float:left;text-align:center;width:200px;height:350px;overflow:hidden">
			select : <?PHP if (get_option('validuserid','an unknown') != "freebie") echo '<input type="radio" name="pj-layout" value="3">'; ?> <br>
			<img src="http://linksalt.com/fmchome/layoutpics/layout3.jpg" width="140"><br>
			<br>A PREMIUM or ELITE layout <br>Chocolate box<br>Hot seller !!!.				
			</div>
			</td>
			
			<td width="200">
			<div style="float:left;text-align:center;width:200px;height:350px;overflow:hidden">
			select : <?PHP if (get_option('validuserid','an unknown') != "freebie") echo '<input type="radio" name="pj-layout" value="4">'; ?> <br>
			<img src="http://linksalt.com/fmchome/layoutpics/layout4.jpg" width="140"><br>
			<br>A PREMIUM or ELITE layout cross browser safe many on a page	
			</div>
			</td>
			
			<td width="200">
			<div style="float:left;text-align:center;width:200px;height:350px;overflow:hidden">
			select : <?PHP if (get_option('validuserid','an unknown') != "freebie") echo '<input type="radio" name="pj-layout" value="5">'; ?> <br>
			<img src="http://linksalt.com/fmchome/layoutpics/layout5.jpg" width="140"><br>
			<br>A PREMIUM or ELITE layout with web 2.0 rounded corners and advanced CSS (modern browsers).		
			</div>
			</td>
			
			</tr></table>
			
			
			<br><br>
			
			<INPUT TYPE=hidden NAME="ID" VALUE="<?PHP echo $selectID; ?> ">
			<INPUT TYPE=hidden NAME="pj-pagenet" VALUE="<?PHP echo $_POST['pj-pagenet']; ?> ">
			<INPUT TYPE=hidden NAME="select1" VALUE="1">
			<input type="submit" style="background-color:yellow" value="build a shopping PAGE with these values"/>
			</form>

	<?PHP
	}
?>





</div>
</div>
<br>

<?PHP include ('pj_footer.php'); ?>	

