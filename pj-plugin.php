<?php
/*
Plugin Name: portaljumper FEED MONSTER
Plugin URI: http://portaljumper.com
Description: turns any selected affiliate datafeed from networks like shareasale, commission junction, linkshare, tradetracker, webgains, clickbank, daisycon, m4n and many more in to full wordpress posts complete with SEO categories, post content and tags. Choose from over 65.000.000 products in thousands of automated datafeeds. Simply select your preferred feeds and start earning a steady income. PREMIUM mode for power-earnings includes more features, enhanced Search engine optimization and  complete white label operations <a href='http://portaljumper.com/feedmonster'>Go premium now !</a>.
Author: pete scheepens
Author URI: http://portaljumper.com
Version: 4.9
*/

include_once('pj_functions.php'); 
// error_reporting(0);

// add scrollcat widget

if(function_exists('l66_load_widgets2') != true)
{
function l66_load_widgets2() {
	register_widget('l66_widget_4');
}
}
// end adding scrollcat widget
add_action( 'widgets_init', 'l66_load_widgets2' );
add_action('shutdown', 'ticktocksurpressed');
add_action('admin_menu', 'pj_page');
add_shortcode('pjcode', 'pj_shortcode');
add_action('admin_head', 'my_custom_logo');
function my_custom_logo() {
   echo '
      <style type="text/css">
         #header-logo { background-image: url(http://linksalt.com/fmchome/logo.jpg) !important; }
      </style>
   ';
}

function pj_page()
{
add_menu_page('pj Datafeeds', 'FEED-MONSTER', 'administrator', 'pj-admin', 'pj_settings');
add_submenu_page('pj-admin', '-- pj-Datafeed -- Upload Datafeed --', 'auto-load posts', 'administrator', 'set_feeds', 'pj_feed_page');
add_submenu_page('pj-admin', '-- pj-Datafeed -- Upload Datafeed --', 'build a shop-page', 'administrator', 'build_page', 'pj_page_builder');
add_submenu_page('pj-admin', '-- pj-Datafeed -- Upload Datafeed --', 'affiliate ad-builder', 'administrator', 'linksalt_ad_builder', 'pj_linksalt');				
add_submenu_page('pj-admin', '-- pj-Datafeed -- Upload Datafeed --', 'delete post tools', 'administrator', 'set_tools', 'pj_set_page');	
add_submenu_page('pj-admin', '-- pj-Datafeed -- Upload Datafeed --', 'forum', 'administrator', 'view_stats', 'pj_stats');	
			
}
function pj_feed_page() {
    include"pj_feed_page.php";
}
function pj_page_builder() {
    include"pj_page_builder.php";
}
function pj_set_page() {
    include"pj_set_page.php";
}
function pj_stats() {
    include"pj_stats.php";
}
function pj_linksalt() {
    include"pj_linksalt.php";
}


function pj_settings() 
	{
	$x = plugin_dir_path(__FILE__);
	
	// start the page layout 
?>
	<iframe src="http://portaljumper.com/wpplugin/plugintop-news.php" width="100%" height="30" scrolling="no">Your system does not support Iframes so you can not see the latest news</iframe>
	<hr>
	Portaljumper.com's datafeed monster will take any one of thousands of datafeeds from dozens of affiliate networks and turn them in to 
	valid WordPress posts, complete with tags and categories. No strain on your database through drip-feed technology. Full SEO benefits etc. <a href="http://portaljumper.com/discuss">get more info on our forum..</a>
	<hr>
	<h2>Please set your affiliate network ID's here !</h2>
	<b>THIS IS VERSION 4.8 </b> --> Please mention bugs and comments on <a href="http://portaljumper.com/discuss" title="visit our combination forum for linksalt, lane66 and feed-monster" target="_blank">our forum</a>.
<?PHP 
		if (isset($_POST['idsetting']))
			{
			echo "<p align='center'><center><div style='background:#ff0;color: red;'><strong>ID settings updated <a href='admin.php?page=set_feeds' title='start the feeding process'>Proceed to network & feed settings.</a></strong></div></p>";
			} 
?>
	<hr>
<?PHP
	// check for cURL support first
	if (_iscurlsupported()) 
		echo "<font color='blue'> (cURL is supported on your system) ... it's a good thing !</font> <br><small>Participating networks were loaded live. <br>for your own sake, click the questionmarks below. Some network ID's are not what you might think ! If you use the example ID's you can test out PREMIUM mode. </small><br>"; else echo "<b><font color='red'>WARNING !! cURL is NOT supported on your system.<br>
		<hr>YOU CAN NOT USE THIS PLUGIN WITHOUT cURL<hr>
		Please contact your host and have cURL installed or switch hosts (Since practically every host provides cURL support, your host obviously sucks). If you 
		are a do-it-your-self kinda person you can reinstall PHP with cURL support. Visit http://www.haxx.se/curl.html or better yet http://php.net/manual/en/book.curl.php for more info</font>";

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
	<form action="admin.php?page=pj-admin" method="post"> 
	<table>
<?PHP
	for ($counter = 0; $counter < $netwcount; $counter++) 
		{
		if (isset($_POST[$networks[$counter]])) 
			{
			update_option($networks[$counter],$_POST[$networks[$counter]]);
			}
			
		if ($networks[$counter] == "shareasale") $examp = "438368";
		if ($networks[$counter] == "clickbank") $examp = "owagu";	
		if ($networks[$counter] == "tradetracker") $examp = "65027";	
		if ($networks[$counter] == "linkshare") $examp = "TCVU2*9nI18";	
		if ($networks[$counter] == "commissionjunction") $examp = "5221670";	
		if ($networks[$counter] == "m4n") $examp = "16326";	
		if ($networks[$counter] == "daisycon") $examp = "113834";	
		if ($networks[$counter] == "webgains") $examp = "85321";	
		if ($networks[$counter] == "pepperjam") $examp = "";	
		if ($networks[$counter] == "affiliate4you") $examp = "1452";	
		if ($networks[$counter] == "affilinet") $examp = "540123";	
		if ($networks[$counter] == "clixgalore") $examp = "232790";	
		if ($networks[$counter] == "tradedoubler_be") $examp = "1941009";	
		if ($networks[$counter] == "tradetracker_nl") $examp = "65027";	
		if ($networks[$counter] == "tradetracker_be") $examp = "77522";	
		if ($networks[$counter] == "tradetracker_fi") $examp = "71528";	
		if ($networks[$counter] == "tradetracker_de") $examp = "65608";	
		if ($networks[$counter] == "tradetracker_uk") $examp = "77636";	
		if ($networks[$counter] == "tradedoubler_nl") $examp = "1941007";	
		if ($networks[$counter] == "tradedoubler_de") $examp = "1946104";	
		if ($networks[$counter] == "tradedoubler_uk") $examp = "1943070";	
		if ($networks[$counter] == "paidonresults") $examp = "25451";	
			
		echo "<tr><td style='width:500px'>Enter a <font color='red'>".$networks[$counter]."</font> affiliate network ID: <a href='http://portaljumper.com/314/how-to-find-all-those-ids/' target='_blank' title='find out where to find your correct ID'> ???</a>
		like: $examp
		</td>
		<td><input name='".$networks[$counter]."' type='text' 
			value='".get_option($networks[$counter])."' />"; 
			
		// now let's go and check what kind of validuserid we have (freebie, PREMIUM, banned)
		$vurl = "http://linksalt.com/fmchome/checkid.php?checkid=".get_option($networks[$counter])."&network=".$networks[$counter];
		$vfg = curl_init();
		curl_setopt($vfg,CURLOPT_URL,$vurl);
		curl_setopt($vfg,CURLOPT_FRESH_CONNECT,TRUE);
		curl_setopt($vfg,CURLOPT_RETURNTRANSFER,1);
		curl_setopt($vfg,CURLOPT_CONNECTTIMEOUT,6);
		$validuser = curl_exec($vfg);
		$validuser = explode("|",$validuser);
		curl_close($vfg);
		if ($validuser[0] == "freebie") 
			{
			echo "<small><font color='gray'> ".$validuser[0]." mode | ".$validuser[1]." hits | <a href='http://portaljumper.com/feedmonster' title='Premium members generate more leads and earn more money'>upgrade</a>.</font></small>";
			}
		elseif ($validuser[0] == "PREMIUM") 
			{
			echo "<font color='darkgreen'><b>PREMIUM mode</b></font>";
			}
		else 
			{
			echo "<small><font color='gray'>No ID yet ? join an affiliate network below.</font></small>";
			}
		echo "</td></tr>";
		}
	?>
	</table>
	<br>
	<INPUT TYPE=hidden NAME="idsetting" VALUE="updated">
	<p align="center"><input type="submit" value="update my ID settings" style="background-color:yellow" /></p>
	</form>
	<hr>
	<small><b>Not a network affiliate yet ? Join :</b> 
	<a href="http://www.shareasale.com/r.cfm?b=1525&u=438368&m=47&urllink=&afftrack=" title="sign up free of charge and start earning affiliate income"> >shareasale(int)</a> |
	<a href="http://tc.tradetracker.net/?c=27&m=39676&a=65027&r=&u=" title="sign up free of charge and start earning affiliate income">Tradetracker</a> |
	<a href="http://cj.com" >C.J.(int)</a> |
	<a href="http://clicks.m4n.nl/_c?aid=16326&adid=63701" title="sign up free of charge and start earning affiliate income">m4n(NL)</a> |
	<a href="http://tc.tradetracker.net/?c=1139&m=40672&a=65608&r=&u=" title="sign up free of charge, courtesy of feed-monster, and start earning affiliate income">Tradetracker</a> |
	<a href="http://kliks.affiliate4you.nl/43/1452/?linkinfo=" target="_blank" rel="nofollow">affiliate4you.nl</a> |
	</small>
	<hr>
	Done with setting network ID's and want to start feeding the monster ? <a href="admin.php?page=set_feeds" title="start the feeding process">Select your stuffings</a>.
	<br>
	<p align="center">Portaljumper.com - feeds monster<br>Program & Design by: Pete Scheepens</p>
	<small><font color='#95B9C7'>* some restrictions may apply on these settings & options. Due to high demand the 
	portaljumper serverfarms are under heavy load and from time to time we must restrict certain services
	 to premium users only. Regular (free) accounts may in certain circumstances share up to every 6th generated link or impression
	 with a premium member ID. <a href="http://portaljumper.com/feedmonster" title="Premium members have been shown to make a monthly average of 624% more affiliate income">go premium today</a>.</font></small>
	<hr>

	<div style="clear:both"></div>
	<?PHP if (file_exists($x.'pj_footer.php')) include ($x.'pj_footer.php'); ?>	


	<?PHP
	}

