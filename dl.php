<?php

file_put_contents("debug_dl",print_r($_SERVER,true));

$sid = $_GET["id"];

if($sid < 0) {
	$sid = -$sid;
    $links = json_decode(file_get_contents("links_cache"),true);
    $url = $links[$sid];
	
    $referer = "http://www.addic7ed.com/show/" . $links[0];


    echo "003d02\n";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/47.0.2526.106 Safari/537.36");
    curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    //curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'curlHeaderCallback');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $exec = curl_exec($ch);
}
else {

    header("Location: http://www.opensubtitles.org/isdb/dl.php?".$_SERVER["QUERY_STRING"]);

}
