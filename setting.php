<?php
require_once('config.php');

$server_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
$dbLink = mysql_select_db(DB_NAME, $server_link);
mysql_query("set names 'utf8'");

$sql_query = "SELECT * FROM `wallpaper_page_sheet`";
$result = mysql_query($sql_query);

$pages_sheet = array();

echo '<html>';
echo "<head><link rel='stylesheet' id='twentyfourteen-style-css'  href='http://samcui.com/wp-content/themes/twentyfourteen/style.css?ver=3.8.4' type='text/css' media='all' /></head>";

echo '<body class="comment-form-author"><article class="post-112 post type-post status-publish format-standard hentry category-uncategorized">';

if ($_POST != null) {
	echo 'POST';
}

if ($_GET != null) {
	echo 'GET';
}

echo '<h3>List:</h3>';
echo '<table>';
echo '<tr><th>uuid</th><th>method</th><th>id</th><th>title</th><th>sort</th><th>min_x</th><th>min_y</th><th>min_num</th><th>rss.theme</th><th>Del(building)</th></tr>';

$p = '<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>';

while ($row = mysql_fetch_assoc($result)){		
	$pages_sheet[] = $row;
	$link = sprintf('<a href="theme.php?title=%s&uuid=%s">Link</a>', $row['title'], $row['uuid']);
	$del = sprintf('<a href="setting.php?del=%s">Del</a>', $row['uuid']);
	printf($p, $row['uuid'], $row['method'], $row['id'], $row['title'], $row['sort'], $row['min_x'], $row['min_y'], $row['min_num'], $link, $del);
}
echo '</table>';
?>

<form name="formStr" enctype="application/x-www-form-urlencoded" method="post" action="">
	<p class="comment-form-author">
		<h3>Add a New One:</h3>
		<table>
			<tr><td><label>method:</label></td><td><input id="method" name="method" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>id:</label></td><td><input id="id" name="id" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>title:</label></td><td><input id="title" name="title" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>sort:</label></td><td><input id="sort" name="sort" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>min_x:</label></td><td><input id="min_x" name="min_x" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>min_y:</label></td><td><input id="min_y" name="min_y" type="text" size="60%" aria-required='true' /></td></tr>
			<tr><td><label>min_num:</label></td><td><input id="min_num" name="min_num" type="text" size="60%" aria-required='true' /></td></tr>
		</table>
		<p><input type="submit" value="Submit(building)" /></p>
	</p>
</form>

</article></body></html>';