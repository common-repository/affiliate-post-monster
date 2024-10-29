
<?PHP
// stats for portaljumper.com plugin
$x = plugin_dir_path(__FILE__);
?>
<div style="text-align:center">
<hr>
Credits:<br>
- Programming/marketing by Pete Scheepens & staff - <a href="http://portaljumper.com">portaljumper</a> & Miss Megan's line of sites<br>
- Beta/bug testing by Andy F - the <a href="http://spy-gear-n-gadgets.com/">Spy Gear</a> guy.<br>
- Beta/bug testing by Mark de Scande - <a href="http://www.bloglines.co.za">www.bloglines.co.za</a><br>
- Beta/bug testing by Bilal Yucel <br>
- Bug-reports & feed delivery by Toni S<br>
- Bug squish pro - Pierre P.<br>
- Backoffice clickmaster - Bear Bear<br>
- linksalt by <a href="http://linksalt.com/">linksalt.com</a><br>
- All our affiliated webmasters, network operators & feed-merchant liaisons
<hr>
<h2>Affiliate merchant and marketeer's network</h2>
Join now .. it's free and you'll get a chance to mingle with some of the high earners, merchants or datafeed controllers.<br>

<?PHP if (get_option('validuserid') == "PREMIUM"){echo "<p align='center'><font color='blue'>Welcome premium member. Is your site showing up in our top-10 ? It should .... listed sites get a ton more clicks.</font></p><hr>";}
else {echo "<p align='center'><font color='blue'>We did not detect premium status. Some options & benefits are hidden .....</font></p><hr>";} ?>
<a href="http://missmegans.nl/" target="_blank">Click to open megan's affiliates in a large window</a><br>
<iframe src="http://missmegans.nl/" width="99%" height="1600"></iframe>

<div style="clear:both"></div>
<?PHP if (file_exists($x.'pj_footer.php')) include ($x.'pj_footer.php'); ?>	
</div>