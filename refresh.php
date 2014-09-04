<?php
require_once('config.php');

ignore_user_abort();
set_time_limit(0);

ini_set('user_agent','Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.146 Safari/537.36;');

$server_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, MYSQL_CLIENT_INTERACTIVE);
$dbLink = mysql_select_db(DB_NAME, $server_link);
mysql_query("set names 'utf8'");

echo '<pre>';

$p_url = 'http://wall.alphacoders.com/%s.php?id=%s&sort=%s&page=%d';

$sql_query = "SELECT * FROM `wallpaper_page_sheet`";
$result = mysql_query($sql_query);

$pages_sheet = array();

while ($row = mysql_fetch_assoc($result)){		
	$pages_sheet[] = $row;
}

$sql_query = "SELECT src,length,title FROM `wallpaper_pic_sheet`";
$result = mysql_query($sql_query);

$pics_sheet = array();

while ($row = mysql_fetch_assoc($result)){		
	$pics_sheet[$row['title']] = $row;
}

echo 'pages_sheet: ';
print_r($pages_sheet);

echo 'pics_sheet: ';
print_r($pics_sheet);

$sql_query = 'TRUNCATE wallpaper_pic_sheet';
mysql_query($sql_query);

mysql_close($server_link);

foreach ($pages_sheet as $page_info) {
	$src_list = refresh_pic($page_info, $p_url, $pics_sheet);
	
	echo 'src_list: ';
	print_r($src_list);
	
	$server_link = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD, MYSQL_CLIENT_INTERACTIVE);
	$dbLink = mysql_select_db(DB_NAME, $server_link);
	mysql_query("set names 'utf8'");
	
	foreach ($src_list as $db_src) {
		$sql_query = 'INSERT INTO `samcuico_test`.`wallpaper_pic_sheet` (`src`, `page_uuid`, `length`, `title`) VALUES (\''
			. $db_src['src'] . '\', \'' . $page_info['uuid'] . '\', \'' . $db_src['length'] . '\', \'' . $db_src['title'] . '\');';
		mysql_query($sql_query);
	}
	
	mysql_close($server_link);
}

echo '</pre>';
	
function refresh_pic($page_info, $p_url, $pics_sheet) {
	$src_list = array();
	$key = 0;
	for ($page = 1; $key <= $page_info['min_num']; $page ++) {
		$url = sprintf($p_url, $page_info['method'], $page_info['id'], $page_info['sort'], $page);
		echo "Scaning: " . $url . PHP_EOL;
		
		$content = file_get_contents($url);
		//print_r($content);
		
		$htmDoc = new DOMDocument();
		@$htmDoc->loadHTML($content);
		$htmDoc->normalizeDocument();
		
		$div_list = $htmDoc->getElementsByTagName('div');
		foreach ($div_list as $div){
			$div_class = $div->getAttribute('class');
			if ($div_class == 'item') {
				$as_list = $div->getElementsByTagName('a');
				foreach ($as_list as $as){
					$as_href = $as->getAttribute('href');
					if (substr($as_href, 0, 10) == 'big.php?i=') {
						$a_src = array();
						$jump = false;
						$info_jump = false;
						
						$img_list = $as->getElementsByTagName('img');
						$jump = true;
						foreach ($img_list as $img){
							if($img->getAttribute('data-src')) {
								$img_src = $img->getAttribute('data-src');
								$jump = false;
							} else if ($img->getAttribute('src')) {
								$img_src = $img->getAttribute('src');
								$jump = false;
							}
							$img_src = get_img_src($img_src);
							if ($pics_sheet[$img_src['title']] != null) {
								$info_jump = true;
								$a_src = $pics_sheet[$img_src['title']];
							} else {
								$a_src['src'] = $img_src['src'];
								$a_src['title'] = $img_src['title'];
								$a_src['length'] = get_pic_length($img_src['src']);
							}
						}
						
						if ((!$info_jump) && (!$jump)) {
							$span_list = $div->getElementsByTagName('span');
							foreach ($span_list as $span){
								if ($span->getAttribute('class') == 'resolution') {
									$strong_list = $span->getElementsByTagName('strong');
									foreach ($strong_list as $strong){
										$strong = explode(' ', $strong->nodeValue);
										$strong = explode('x', $strong[0]);
										if ((int)($strong[0]) < (int)$page_info['min_x'] || (int)($strong[1]) < (int)$page_info['min_y']) {
											$jump = true;
										}
									}
								}
							}
						}
						
						if (!$jump) {
							$src_list[] = $a_src;
							$key++;
						}
						
					}
				}
			}
		}
	}
	return $src_list;
}

function get_img_src($img_src) {
	$paths = explode('/', substr($img_src, 7));
	$last_path = explode('-', $paths[2]);
	$return_arr = array();
	$return_arr['src'] = 'http://' . $paths[0] . '/' . $paths[1] . '/' . $last_path[2];
	$last_path = explode('.', $last_path[2]);
	$return_arr['title'] = $last_path[0];
	return $return_arr;
}

function get_pic_length($url) {
	$url = parse_url($url);
	if($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$error)){
		fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
		fputs($fp,"Host:$url[host]\r\n\r\n");
		while(!feof($fp)){
			$tmp = fgets($fp);
			if(trim($tmp) == ''){
				break;
			} else if (preg_match('/Content-Length:(.*)/si',$tmp,$arr)) {
				return trim($arr[1]);
			}
		}
		return null;
	} else {
		return null;
	}
}
?>