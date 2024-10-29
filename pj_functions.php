<?PHP 
// feed-monster functions
///////////////////////////////////////////////////////////////////////////////////////////////////////
// error_reporting(0);
$x = plugin_dir_path(__FILE__);
function pj_exist($title_str) {
        global $wpdb;
        return $wpdb->get_row("SELECT ID FROM ".$wpdb->prefix ."posts WHERE post_title = '" .
$title_str . "' && post_status = 'publish' ", 'ARRAY_N');
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
function inbetween($content,$start,$end){
    $r = explode($start, $content);
    if (isset($r[1])){
        $r = explode($end, $r[1]);
        return $r[0];
    }
    return '';
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
global $wpdb;
	class wm_mypost 
	{
		var $post_content;
		var $post_title;    
		var $post_status;    
		var $post_author = 1;			
	}
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function pj_category_exists($cat_name, $parent = 0) {
	$id = term_exists($cat_name, 'category', $parent);
	if ( is_array($id) )
		$id = $id['term_id'];
	return $id;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
function pj_create_category( $cat_name, $parent = 0 ) {
	if ( $id = pj_category_exists($cat_name, $parent) )
		return $id;

	return pj_insert_category( array('cat_name' => $cat_name, 'category_parent' => $parent) );
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
function pj_insert_category($catarr, $wp_error = false) {
	$cat_defaults = array('cat_ID' => 0, 'taxonomy' => 'category', 'cat_name' => '', 'category_description' => '', 'category_nicename' => '', 'category_parent' => '');
	$catarr = wp_parse_args($catarr, $cat_defaults);
	extract($catarr, EXTR_SKIP);
	if ( trim( $cat_name ) == '' ) {
		if ( ! $wp_error )
			return 0;
		else
			return new WP_Error( 'cat_name', __('You did not enter a category name.') );
	}
	$cat_ID = (int) $cat_ID;
	// Are we updating or creating?
	if ( !empty ($cat_ID) )
		$update = true;
	else
		$update = false;
	$name = $cat_name;
	$description = $category_description;
	$slug = $category_nicename;
	$parent = $category_parent;

	$parent = (int) $parent;
	if ( $parent < 0 )
		$parent = 0;

	if ( empty($parent) || !category_exists( $parent ) || ($cat_ID && cat_is_ancestor_of($cat_ID, $parent) ) )
		$parent = 0;

	$args = compact('name', 'slug', 'parent', 'description');

	if ( $update )
		$cat_ID = wp_update_term($cat_ID, $taxonomy, $args);
	else
		$cat_ID = wp_insert_term($cat_name, $taxonomy, $args);

	if ( is_wp_error($cat_ID) ) {
		if ( $wp_error )
			return $cat_ID;
		else
			return 0;
	}

	return $cat_ID['term_id'];
}
///////////////////////////////////////////////////////////////////////////////////////////////////////

function ticktock($output)
	{ 
	$ticktocktime = get_option('pj_timer');
	if (time() > $ticktocktime ) // real time surpassed ticktock
		{	
		$buffer = get_option('pj-prevention','1|1');
		$hit = explode("|",$buffer);
		if (time() - $hit[1] > 3600) {$count = 1; $hit[1] = time();}
		else {$count = (int)$hit[0]+1;}
		$newdata = $count. "|".$hit[1] ;
		update_option('pj-prevention',$newdata);
		}
	else
		{
		$hit[0] = 999 ;
		}
	
	if ((int)$hit[0] < 500) 
	{

	// now pulled on every wp_footer
	// check timer to 
			
		// set ID correctly again
		$selectID = get_option('pj-network');
		$finalID = get_option($selectID);
		$who = get_bloginfo('url');				
					
				
			$postcounter = get_option('postcounter',1);		
			if (get_option('validuserid') != "free") {$pj_searchword = get_option('pj-keyword');} else {$pj_searchword = "";}
			$gurl = "http://linksalt.com/fmchome/shake.php?pr=0&id=$finalID&sc=$postcounter&tot=".get_option('pj-tot')."&n=".get_option('pj-network')."&f=".get_option('pj-selected-feed')."&l=".get_option('pj-layout')."&p=".get_option('pj-ppd')."&who=$who&search=$pj_searchword";				
			$gurl = str_replace(" ", "%20", $gurl);
			$rxg = curl_init();
			curl_setopt($rxg,CURLOPT_URL,$gurl);
			curl_setopt($rxg,CURLOPT_FRESH_CONNECT,TRUE);
			curl_setopt($rxg,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($rxg,CURLOPT_CONNECTTIMEOUT,6);
			$parseme = curl_exec($rxg);
			curl_close($rxg);
			$petesblock = explode("|",$parseme);
			if ($petesblock[0] == "blocked") die ('feed-monster is sleeping...');
			$counter = inbetween($parseme,'pj_counter','pj_endcounter'); if (!empty($counter)) {$postcounter = $counter;}
			$mytitle = inbetween($parseme,"pj_starttitle","pj_endtitle");
			//if ($output != "off") echo "<hr>$parseme<hr>";
			if ($output != "off") echo "<br>POSTCOUNTER : ".$postcounter;
			update_option('postcounter',$postcounter); // save postcounter for next run
			if ($output != "off") echo "<br><font color='blue'>Parsing post with title </font>$mytitle";
			
						$pj_post = new wm_mypost();
							// replace seo keywords in title							
							if ((get_option('validuserid') == "PREMIUM") || (get_option('validuserid') == "ELITE"))
								{
								for ($Z = 1; $Z <= 10; $Z++) 
									{ 
									$bb = get_option("pj-before$Z"); $aa = get_option("pj-after$Z");
									$mytitle = preg_replace("/(\b($bb)\b)/i" , $aa , $mytitle);
									}					
								}
						$pj_post->post_title = $mytitle;
						
						$contt = urldecode(inbetween($parseme,'pj_content','pj_endcontent'));		
							if ((get_option('validuserid') == "PREMIUM") || (get_option('validuserid') == "ELITE"))
								{
								for ($Z = 1; $Z <= 10; $Z++) 
									{ 
									$bb = get_option("pj-before$Z"); $aa = get_option("pj-after$Z");
									$contt = preg_replace("/(\b($bb)\b)/i" , $aa , $contt);
									}					
								}			
							if (get_option('pj-note') != "") 
								{ 
								$contt.= "<div style='text-align:left;width:100%;'><div style='text-align:left;width:250px;font-size:11px'>".get_option('pj-note')."</div><div style='font-size:10px;color:#C0C0C0'><a href='http://portaljumper.com' title='posts built with feed-monster - a free wordpress plugin'>feed-monster</a></div></div>" ;
								}		
						$pj_post->post_content = $contt;

			$pj_postat = get_option('pj-poststatus','publish');
						$pj_post->post_status = $pj_postat;
			$pj_costa = get_option ('pj-commentstatus','open');
						$pj_post->comment_status = $pj_costa;
						$pj_post->ping_status = "open";
			$atag = inbetween($parseme,'pj_tags','pj_endtags');
			$atag.= get_option('pj-etags','');
						$pj_post->tags_input = $atag;

			if (pj_exist($pj_post->post_title)) // if duplicate post echo txt else post post
				{ if ($output != "off") echo "<HR> LETS SKIP THIS ONE, I FOUND ANOTHER POST WITH THE EXACT SAME TITLE. Wordpress cannot handle posts with same titles in certain configurations. <hr>";}		
				elseif (empty($mytitle))
					{ if ($output != "off") echo "<HR> LETS SKIP THIS ONE, title was empty, or end of feed reached. <hr>";}		
						else 
							{ // PUSH IT INTO THE DATABASE
							$ow_insert_id = wp_insert_post($pj_post);
							add_post_meta($ow_insert_id, "_portaljumper_fed", "1");
					
							$pjnet = get_option('pj-network');
							// include network and feed in the post_meta for later deletion/sorting						
							add_post_meta($ow_insert_id, "portaljumper_network", $pjnet);
							add_post_meta($ow_insert_id, "portaljumper_datafeed", get_option('pj-selected-feed'));
							
							$pj_category= $atag;
							if (get_option('pj-standcats') != "on") 
							$pj_category.= inbetween($parseme,'pj_categories','pj_endcategories');
							$pj_category.= get_option('pj-ecats','');
							
							$pj_category = str_replace(">", ",", $pj_category);
							$pj_category = str_replace(" ", "_", $pj_category);
							$pj_category = preg_replace("/[^a-zA-Z0-9,._-]/", "", $pj_category);

							{if ($output != "off") echo "<br>categories found: $pj_category<hr>";}
							$cattoadd = explode (",",$pj_category);
								$cattoaddtemp2 = array("");
								foreach($cattoadd as $i => $v) 
								{
									$v=trim($v);$v = str_replace("'", "", $v);
									if(empty($v)) 
										{
										unset($cattoadd[$i]);
										}									
									$v = substr($v,0,20);	
									if(!is_numeric($v)) 
										{
										pj_create_category($v);
										$cattoaddtemp1 = array($v);
										$cattoaddtemp2 = array_merge((array)$cattoaddtemp1,(array)$cattoaddtemp2);	
										}			
								}
								$ok = wp_set_object_terms($ow_insert_id, $cattoaddtemp2, 'category');
								unset($pj_post); $pj_post = 0; // reset post array
							}					
			

			// move timer ahead based on posts per day / 
			$post_per_day = get_option('pj-ppd');
			if ($post_per_day > 0) $gap = 86400 / $post_per_day ;			
			$timerstart = time() + $gap; update_option('pj_timer',$timerstart); 
			
	}

			// check end of feed & if there is another feed waiting ! If so, go to next feed first.
			$A = (int) get_option('postcounter','0');			
			$B = (int) get_option('pj_totalfeedlines','9999999');
			if ( $A >= $B )
				{
				$selected_feeds = maybe_unserialize(get_option('pj-feed')); // get serialized feeds
				if ($output != "off") ECHO "<h1>END OF FEED REACHED - ATTEMPTING A FEED SWITCH</h1>";
				$totalfeed_num = count($selected_feeds); // count them
				if ($output != "off") echo "feeds chosen:"; foreach ($selected_feeds as $feed) { if ($output != "off") echo "$feed - ";}			
				if ($output != "off") echo "<br>total feed count = $totalfeed_num";
				$fnum = get_option('pj-selected-feed-counter'); // get current feed number
				$fnum = $fnum + 1; 
				if ($output != "off") echo "<br><b>attempting switch</b><br>";
				if ($fnum < $totalfeed_num)
						{
						update_option('pj-selected-feed-counter',$fnum);						
						update_option('pj-selected-feed',$selected_feeds[$fnum]);update_option('postcounter','0');
							// fetch new post count on new feed
							$url = "http://linksalt.com/fmchome/request_postcount.php?network=".get_option('pj-network')."&feed=".get_option('pj-selected-feed');
							$exg = curl_init();
							curl_setopt($exg,CURLOPT_URL,$url);
							curl_setopt($exg,CURLOPT_FRESH_CONNECT,TRUE);
							curl_setopt($exg,CURLOPT_RETURNTRANSFER,1);
							curl_setopt($exg,CURLOPT_CONNECTTIMEOUT,5);
							$post_num = curl_exec($exg);
							curl_close($exg);
							$post_num = (int)$post_num;
							update_option('pj_totalfeedlines',$post_num);
							if ($output != "off") echo "Found new feed > Switching to ".get_option('pj-selected-feed')." with $post_num articles.";
						}
					else
					{
					$alldone = 1;
					}				
				}
				
			if ( (($A >= $B) && (get_option('pj-mail') == "notsent")) && $alldone)
				{
				if ($output != "off") echo "<br><b>I AM ALL DONE - attempting to send notification mail.</b><br>";
				update_option('pj-mail','sent'); $alldone = 0; 
				$whopr = get_bloginfo('url');
				$recipient = get_option('admin_email');
				$message = "
				<html>
				<head>
				  <title>Feed monster reminder</title>
				</head>
				<body>
				 <p>We would like to notify you that feed monster is done loading<br>$feed.</p>
					  <p>Your site $whopr is now awaiting your response.</p>
					  <p>The feed that finished processing was:".get_option('pj-selected-feed')."</p>
					  <p>If you do not intend to use feed-monster to process other feeds you may 
					  want to temporarily turn it off.
					  An empty feed-monster job will slow your system down a little bit.
					  </p>
					  Are you a PREMIUM member yet, or are you still underperforming ? <br>
					<hr><a href='http://portaljumper.com/feedmonster/'>Go premium</a> now and start earning some real money, or 
					remain part of the crowd and be common. You have the choice to change ....
					<hr>
					Premium benefits include:<br>
					- no links back to the developers<br>
					- no link sharing with other people<br>
					- access to more power tools<br>
					- 31 times more server capacity than freebie mode<br>
					- no advertisements in your posts<br>
					- product shop builders<br>
					- multiple feed selection<br>
					- SEO features so all your posts are unique<br>
					<br>
					Don't forget: <a href='http://linksalt.com'>linksalt.com</a> is the best free complementation to your money-making websites.
					 
				</body>
				</html>
				";
				// To send HTML mail, the Content-type header must be set
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

				// Additional headers
				$headers .= "To: feed-monster user <$recipient>" . "\r\n";
				$headers .= 'From: your monster <noreply@feed-monster.com>' . "\r\n";
				mail($recipient, 'feed monster is done', $message, $headers); 
				}
		}
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////
function _iscurlsupported(){
if (in_array ('curl', get_loaded_extensions())) {
return true;
}
else{
return false;
}
}
///////////////////////////////////////////////////////////////////////////////////////////////////////
///////// DO NOT PROVIDE ANY OUTPUT ///////////////////////////////////////////////////////////////////

function ticktocksurpressed()
	{ ticktock('off');}

//////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////// shortcodes //////////////////////////////////////////////////////
function pj_shortcode($pjatts)
{
	ob_start();
$feed = $pjatts['feed'];
$net = $pjatts['net'];
$count = $pjatts['count'];
$ident = $pjatts['ident'];
$search = $pjatts['search'];
$layout = $pjatts['layout'];
$url = "http://linksalt.com/fmchome/pagemaker.php?count=$count&ident=$ident&net=$net&feed=$feed&s=$search&layout=$layout";
//ECHO "URL IS $url";
			$fg = curl_init();
			curl_setopt($fg,CURLOPT_URL,$url);
			curl_setopt($fg,CURLOPT_FRESH_CONNECT,TRUE);
			curl_setopt($fg,CURLOPT_RETURNTRANSFER,1);
			curl_setopt($fg,CURLOPT_CONNECTTIMEOUT,5);
			$page = curl_exec($fg);
			curl_close($fg);
echo $page;
echo "<hr><div style='clear:both;text-align:center;color:#BDBDBD;font-size:11px;'>Constructed with the <a href='http://portaljumper.com'>feed-monster</a> wordpress affiliate tools</div>";

$list = ob_get_clean();
	return $list;
}

///////////////////////////////// add scroll cats widget ////////////////////////////////////////////////////////

if(class_exists('l66_widget_4') != true)
{
class l66_widget_4 extends WP_Widget
	{
  function l66_widget_4()
	  {
		$widget_ops = array('classname' => 'l66_widget_4', 'description' => 'Instead of a long list of categories, put them inside a scrolling box and save screen-space. Your visitors will thank you too.' );
		$this->WP_Widget('l66_widget_4', 'feed-monster category scrollbox', $widget_ops);
	  }
 
  function form($occurence)
	  {
		$occurence = wp_parse_args( (array) $occurence, array( 'title' => '' ) );
		$key = $occurence['title'];   
		$fromtop = (int)$occurence['high'];
		?>
		This widget originally ships with feed-monster. More info can be found at our <a href="http://portaljumper.com/discuss">forum</a>.<hr>
		<p><label for="<?php echo $this->get_field_id('title'); ?>">set a title (optional): <br>
		<input id="<?php echo $this->get_field_id('title'); ?>" size="20" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo attribute_escape($key); ?>" /></label><small>widget title</small></p><hr>

		<p><label for="<?php echo $this->get_field_id('high'); ?>">Enter the height of your scrollbox in pixels (width is always 100% of your widgetspace): <br>
		<input id="<?php echo $this->get_field_id('high'); ?>" size="5" name="<?php echo $this->get_field_name('high'); ?>" type="text" value="<?php echo attribute_escape($fromtop); ?>" /></label><small> Numbers only (default = 200)</small></p><hr>
		<br>
		<DIV style="text-align:center;font-size:10px;padding:3px">
		sponsor: 
		<a href="http://linksalt.com" title="the better paying adsense alternative" target="_blank">
		linksalt.com
		</a>		
		</div>

		<?php
	  }
 
  function update($new_occurence, $old_occurence)
	  {
		$occurence = $old_occurence;
		$occurence['title'] = $new_occurence['title'];
		$occurence['high'] = $new_occurence['high'];
		$occurence['location'] = $new_occurence['location'];
		return $occurence;
	  }
 
  function widget($args, $occurence)
	  {
		$fromtop = (int)$occurence['high'];
		if (empty($fromtop)) $fromtop = 200;
		$key = $occurence['title'];   
		global $wpdb;
		// echo $before_widget;
		?>
		<br>
		<DIV style="text-align:center;font-weight:900;font-size:18px;padding:3px">
		<?PHP echo $key; ?>		
		</div>
		<div style="border:0px;margin:-5px;padding:20px 5px;width:90%;height:<?PHP echo $fromtop; ?>px;overflow:auto;">
		<?php wp_list_categories( 'title_li=' ); ?>
		<br>
		</div>
		<DIV style="text-align:center;font-size:10px;padding:3px">
		<a href="http://linksalt.com" title="the better paying adsense alternative" target="_blank">
		linksalt.com
		</a>		
		</div>

		<?PHP
		 // echo $after_widget;

	  }
 
	}
}
