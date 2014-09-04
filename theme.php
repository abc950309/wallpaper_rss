<?php
require_once('config.php');

$theme = file_get_contents('rss.theme');

$theme = str_replace('%DisplayName%', $_GET['title'], $theme);
$theme = str_replace('%rss_url%', dirname('http://' . $_SERVER['SERVER_NAME'] . $_SERVER["REQUEST_URI"]) . '/rss.php?uuid=' . $_GET['uuid'], $theme);

header('Content-type:text/html;charset=utf-8');
Header('Content-type: application/octet-stream');
Header('Accept-Ranges: bytes');
Header('Accept-Length:' . strlen($theme));
Header('Content-Disposition: attachment; filename=' . $_GET['title'] . '.theme');
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");

echo $theme;
?>