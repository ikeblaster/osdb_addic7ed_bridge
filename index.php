<?php

file_put_contents("debug_index",print_r($_SERVER,true)); 

$details = null;

if(!file_exists("_osdb")) {
    require "addic7ed.php";

    $filename = $_GET["name"][0];
    $filename = str_replace(array("TwoDDL_"),"",$filename);

    $addic7ed = new addic7ed();
    $details = $addic7ed->parseFilename($filename);
}

$subtitles = array();

if($details != null) {
    $subtitles = $addic7ed->getSubtitlesForFile($filename);
}

//var_dump($subtitles);

if(!empty($subtitles)) {
	echo "ticket=".time()."\n";
	
    $i = substr(time(),-6) * 100;
    $cache = array();
	
	$cache[0] = $subtitles[0]->showid;

    foreach($subtitles as $subs) {
        if($subs->episode != $details->episode) continue;

        $cache[$i] = $subs->link;

        echo "movie={$filename}|\n";
        echo "subtitle=-{$i}\n";
        echo "name={$subs->version} {$subs->hd} {$subs->hi}\n";
        echo "discs=1\n";
        echo "disc_no=1\n";
        echo "format=srt\n";
        echo "iso639_2=\n";
        echo "language={$subs->language}\n";
        echo "nick=\n";
        echo "email=MAILS_ARE_PROTECTED:)\n";
        echo "endsubtitle\n";
        echo "endmovie\n";

        $i++;
    }

    file_put_contents("links_cache",json_encode($cache));

    echo "end";
}
else {
    header("Location: http://www.opensubtitles.org/isdb/index.php?".$_SERVER["QUERY_STRING"]);
}
