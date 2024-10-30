<?php
include('array2json.php');
$url = urldecode($_GET['url']);
$doc = new DOMDocument();
$doc->load( $url);
$entrys = $doc->getElementsByTagName( "entry" );
$data = array();
$thumbs = array();
$full = array();
foreach( $entrys as $entry)  {
	$thumbnail = $entry->getElementsByTagNameNS( 'http://search.yahoo.com/mrss/','thumbnail' );
	$content = $entry->getElementsByTagNameNS( 'http://search.yahoo.com/mrss/','content' );
	$thumbUrl = $thumbnail->item(0)->getAttribute('url');
	$fullUrl = $content->item(0)->getAttribute('url');
	array_push($thumbs,$thumbUrl);
	array_push($full,$fullUrl);
}
array_push($data,$thumbs, $full);
$json = json_encode2($data);
echo $json;
?>
