<?php


class addic7ed {

	protected $api_url = "http://www.addic7ed.com"; 
	protected $langs = "|1|"; 

    function __construct() {
    }

    function fetch_data($uri) {
        $url = $this->api_url . $uri;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_FAILONERROR, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIE, "PHPSESSID=0sfg8890ak7v3i85q3ivcan8e4");
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.3; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2225.0 Safari/537.36');
        $data = curl_exec($curl);
        
        //if(file_exists("tmp")) $url = "tmp";
        //$data = file_get_contents($url);

        if($data != "" && $url != "tmp") file_put_contents("tmp",$data);
        return $data;
    }

    function getShowId($name) {
		$name = preg_replace('~S(\d{1,2})E(\d{1,3}).*~i', "S\\1E\\2", $name);
		$name = preg_replace('~([^\dhx])(\d)(\d\d)[^\dp].*~i', "\\1S0\\2E\\3", $name);
        $data = $this->fetch_data("/search.php?Submit=Search&search=" . urlencode($name));
		
        if($pos = strpos($data,'id="footermenu"')) $data = substr($data, 0, $pos);
        if(preg_match('~/show/(\d+)~', $data, $matches)) {
            return $matches[1];
        }
        if(preg_match('~href="(serie/[^"]+)"~', $data, $matches)) {
            $data = $this->fetch_data("/".$matches[1]);
            if(preg_match('~/show/(\d+)~', $data, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    function parseFilename($filename) {
        $filename = str_replace(array("720","1080","264","265"),"",$filename);

        if (preg_match('~S(\d{1,2})E(\d{1,3})~i', $filename, $matches)) {
            return (object) array(
                "season" => intval($matches[1]),
                "episode" => intval($matches[2]),
            );
        }
        if (preg_match('~(\d{1,2})x(\d{1,3})~i', $filename, $matches)) {
            return (object) array(
                "season" => intval($matches[1]),
                "episode" => intval($matches[2]),
            );
        }
        if (preg_match('~[^\dhx](\d)(\d\d)[^\dp]~i', $filename, $matches)) {
            return (object) array(
                "season" => intval($matches[1]),
                "episode" => intval($matches[2]),
            );
        }
        return null;
    }

    function getSubtitlesForFile($filename) {
        $showId = $this->getShowId($filename);
        if($showId == null) return;

        $details = $this->parseFilename($filename);
        if($details == null) return;
        

        return $this->getSubtitlesForShow($showId, $details->season);
    }

    function getSubtitlesForShow($showId, $season=1) {
        $data = $this->fetch_data("/ajax_loadShow.php?hd=undefined&hi=undefined&langs={$this->langs}&show={$showId}&season={$season}");
        $data = explode('<tr class=', $data);
        $results = array();

        foreach($data as $row) {
            if(strpos($row, '>Download</a>') === false) continue;
            $row = substr($row, 0, strpos($row, '</tr>'));

            if(preg_match('~<td>(?<season>[^>]*)</td>
                            <td>(?<episode>[^>]*)</td>
                            <td><a[^>]+>(?<title>[^<]+)</a></td>
                            <td>(?<language>[^<]+)</td>
                            <td[^>]*>(?<version>[^<]*)</td> \s*
                            <td[^>]*>(?<completed>[^<]*)</td> \s*
                            <td[^>]*>(?<hi>[^<]*)</td> \s*
                            <td[^>]*>(?<corrected>[^<]*)</td> \s*
                            <td[^>]*>(?<hd>[^<]*)</td> \s*
                            <td[^>]*><a\ href="(?<link>[^"]*)">Download~sx', $row, $matches)) {

                $results[] = (object) array(
                    "season"    => $matches["season"],
                    "episode"   => $matches["episode"],
                    "title"     => $matches["title"],
                    "language"  => $matches["language"],
                    "version"   => $matches["version"],
                    "completed" => $matches["completed"],
                    "hi"        => $matches["hi"] != "" ? "HI" : "",
                    "corrected" => $matches["corrected"] != "" ? "CRRCTD" : "",
                    "hd"        => $matches["hd"] != "" ? "HD" : "",
                    "link"      => $this->api_url . $matches["link"],
                    "showid"    => $showId,
                );
            }
        }

        return $results;
    }

}
