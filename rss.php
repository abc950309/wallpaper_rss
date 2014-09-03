<?php
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWORD', '');
define('DB_HOST', '');

$self_url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"];

$p_items = '<item><title>%s</title><link>%s</link><description></description><enclosure url="%s" length="%s" type="image/jpeg"/></item>';

$p_rss = '<?xml version="1.0" encoding="utf-8"?><rss xmlns:atom="http://www.w3.org/2005/Atom" version="2.0" xml:base="http://test.samcui.com/tools/wallpaper_rss/"><channel><title>%s</title><description></description><link>http://test.samcui.com/tools/wallpaper_rss/</link><atom:link rel="self" href="%s"/><language>en-us</language>%s</channel></rss>';

	$server_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	$dbLink = mysql_select_db(DB_NAME, $server_link);
	mysql_query("set names 'utf8'");
	
	$uuid = $_GET['uuid'];
	
	$sql_query = "SELECT src,length,title FROM wallpaper_pic_sheet WHERE page_uuid='" . $uuid . "'";
	$result = mysql_query($sql_query);
	
	$rss_items = "";
	
	while ($row = mysql_fetch_assoc($result)){
		$rss_items .= sprintf($p_items, $row['title'], $row['src'], $row['src'], $row['length']);
	}
	
	$sql_query = "SELECT title FROM wallpaper_page_sheet WHERE uuid='" . $uuid . "'";
	$result = mysql_query($sql_query);
	
	if ($row = mysql_fetch_assoc($result)) {
		$title = $row['title'];
	} else {
		$title = 'Error';
	}
	
	$rss = sprintf($p_rss, $title, $self_url, $rss_items);
	
	echo $rss;
?>