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
// error_reporting(E_ALL);
// functions for portaljumper.com plugin
$x = plugin_dir_path(__FILE__);
?>
<div class="round">
	<div class="titlebox">DELETE unwanted feeds & posts</div>
	<br>
<h2>PJ's Feed Monster - tools & settings</h2>
<?PHP
if (get_option('validuserid') == "freebie")
{
echo "We're sorry, but it appears that you are not currently using a premium affiliate ID.<br> The powertools on this page are reserved for our premium ID holders only.
<br>If you are a premium member and are seeing this message by error you may need to switch to your premium network first in the set-feeds screen.
<hr>
To become a premium member please go <a href='http://portaljumper.com/feedmonster'>here</a> !
<hr>";
} else {echo "<font color='blue'>Welcome PREMIUM member. All buttons have been unlocked for you. Please be careful when deleting items.</font><hr>";}
?>
Please note that the tools on this page can permanently delete items. Use them with care.<br>
Have a feature request ? <a href="http://portaljumper.com/register" target="_blank">Register</a> at portaljumper.com and <br>
participate in the group discussions at: <a href="http://portaljumper.com/groups/feed-monster/forum/" target="_blank">http://portaljumper.com/groups/feed-monster/forum/</a>.
<br>
</div>
<?PHP
        global $wpdb;	
		if (isset($_POST['pj_action']))
			{
				?>
				<br>
				<div class="round">
				<div class="titlebox">now deleting items ...</div>
				<br>
				<div style="height:150px;width:90%;overflow:scroll">
				<?PHP
				foreach ($_POST['pj_del_feed'] as $feed)
				{
				echo "<h3>Deleting feed $feed</h3>";

				$del_feed = mysql_query("select DISTINCT post_id from $wpdb->postmeta WHERE meta_value = '$feed' ");
				while($row = mysql_fetch_array($del_feed))
					{
					echo "Now deleting post with ID : ".$row['post_id']."<br>";
					$force_delete = TRUE;
					wp_delete_post($row['post_id'], $force_delete);
					}
				}
				echo "All the selected posts have been sucessfully deleted.";
				?>
				</div>
				</div>
				<?PHP
			}
		if (isset($_POST['pj_action2']))
			{
				?>
				<br>
				<div class="round">
				<div class="titlebox">now deleting feeds ...</div>
				<br>
				<div style="height:150px;width:90%;overflow:scroll">
				<?PHP
				foreach ($_POST['pj_del_feedposts'] as $feed)
				{
				echo "<h3>Deleting post $feed</h3>";

				$del_feed = mysql_query("select DISTINCT post_id from $wpdb->postmeta WHERE meta_value = '$feed' ");
				while($row = mysql_fetch_array($del_feed))
					{
					echo "Now deleting post with ID : ".$row['post_id']."<br>";
					$force_delete = TRUE;
					wp_delete_post($row['post_id'], $force_delete);
					}
				}
				echo "All the selected posts have been sucessfully deleted.";
				?>
				</div>
				</div>
				<?PHP
			}
?>
<br>
<div class="round">
<div class="titlebox">select your items ...</div>
<br>

	<div style="background:#ff0;text-align:center;color: red;"><p><strong>Pete says: Use with caution.... Think twice, act once !</strong></p></div>

	<h3>Select to delete by Portaljumper FEED</h3>
<form name="pj_form" action="admin.php?page=set_tools" method="POST">
<?php
        $wp_query = new WP_Query;
		// select by datafeeds                        SELECT affid,SUM(totalhits) FROM referrals GROUP BY affid ORDER BY SUM(totalhits) DESC ||| WHERE meta_key = 'portaljumper_datafeed'
        $del_feed = $wpdb->get_results($wpdb->prepare("select distinct meta_value,COUNT(*) as num from $wpdb->postmeta WHERE meta_key = 'portaljumper_network' GROUP BY meta_value ORDER BY num"));
		foreach ($del_feed as $del_f) 
		{
        echo "<input name='pj_del_feed[]' value = '$del_f->meta_value' type = 'checkbox' /> $del_f->meta_value ( $del_f->num posts)<br>";
        }
echo '<input type="hidden" name="pj_action" value="power-delete" />';     
if (get_option('validuserid') == "freebie") {echo "<br><strong>Sorry, only premium account holders can use this feature</strong><a href='http://portaljumper.com/feedmonster'>go premium now</a>";} else {echo '<input type="submit" name="submit" value="delete network(s))" style="background-color:red">';}
wp_nonce_field('pj-delete'); 
?>
</form>      
<form name="pj_form" action="admin.php?page=set_tools" method="POST">
<?php
        $wp_query = new WP_Query;
		// select by datafeeds                        SELECT affid,SUM(totalhits) FROM referrals GROUP BY affid ORDER BY SUM(totalhits) DESC ||| WHERE meta_key = 'portaljumper_datafeed'
        $del_feed = $wpdb->get_results($wpdb->prepare("select distinct meta_value,COUNT(*) as num from $wpdb->postmeta WHERE meta_key = 'portaljumper_datafeed' GROUP BY meta_value ORDER BY num"));
		foreach ($del_feed as $del_f) 
		{
        echo "<input name='pj_del_feedposts[]' value = '$del_f->meta_value' type = 'checkbox' /> $del_f->meta_value ( $del_f->num posts)<br>";
        }
echo '<input type="hidden" name="pj_action2" value="power-delete" />';     
if (get_option('validuserid') == "freebie") {echo "<br><strong>Sorry, only premium & elite account holders can use this feature</strong><a href='http://portaljumper.com/feedmonster'>go premium now</a>";} else {echo '<input type="submit" name="submit" value="delete feed(s))" style="background-color:red">';}
wp_nonce_field('pj-delete'); 
?>
</form> 
       
</div>

<br>
<div class="round">
<div class="titlebox">WARNING ! THIS OPTION WILL ERASE ALL POSTS,PAGES,TAGS & CATS</div>
<br>
<div style="background:#ff0;text-align:center;color: red;"><p><strong>Pete says: Please think twice !</strong></p></div> This option instantly erases ALL your posts, ALL your Categories, 
ALL your pages and ALL your tags. There is no way back and there is no further warning after you click the big yellow button below. 
Once you click you will have a clean install in less than 1 second ! 
All the options from the option tables will stay intact so all your plugin options will stay the same.
<div style="text-align:center;height:50px;background-color:red">
	<br>
	<form method="post" name="reset">
	<INPUT TYPE=hidden NAME="dbflush" VALUE="go">
<?PHP if (get_option('validuserid') == "freebie") {echo "<br><strong>Sorry, only premium & elite account holders can use this feature</strong><a href='http://portaljumper.com/feedmonster'>go premium now</a>";} else {echo '<input type="submit" value="flush the entire database (CAREFUL !) | 1-click to destroy ALL posts,pages,cats,and tags" style="background-color:yellow"/>'; } ?>
	</form>
	</div>
<?PHP
if (isset($_POST['dbflush']))
	{
	if ($_POST['dbflush'] == "go") 
			{
			global $wpdb;
			$wpdb->query("TRUNCATE `".$wpdb->prefix."postmeta`");
			$wpdb->query("TRUNCATE `".$wpdb->prefix."posts`");
			$wpdb->query("TRUNCATE `".$wpdb->prefix."terms`");
			$wpdb->query("TRUNCATE `".$wpdb->prefix."term_relationships`");
			$wpdb->query("TRUNCATE `".$wpdb->prefix."term_taxonomy`");
			echo "<center><h1>Hope you were sure ! All tables were dropped</h1>You can now continue with a fresh and clean wordpress (although all option tables etc. are still intact)";
			} 
	}
?>
</div>

<div style="clear:both"></div>
<?PHP if (file_exists($x.'pj_footer.php')) include ($x.'pj_footer.php'); ?>	

