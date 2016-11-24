<?php
	
$srv = $_GET["srv"];
$q = $_GET["q"];

$q = str_replace("TwoDDL_","",$q);

if($srv == "subscene") header("Location: http://www.google.com/search?q=".$q."+site:subscene.com");
elseif($srv == "titulky") header("Location: http://www.google.com/search?q=".$q."+site:titulky.com");
elseif($srv == "csfd") header("Location: http://www.csfd.cz/hledat/?q=".$q);
