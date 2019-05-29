<?php
header("Content-type: application/json; charset=utf-8");
include("conn.php");

$q = "";
$lat = 0;
$lng = 0;
$radius = 0;

if(isset($_GET['q']) && $_GET['q']!=""){$q = $_GET['q'];}
if(isset($_GET['lat']) && $_GET['lat']!=""){$lat = $_GET['lat'];}
if(isset($_GET['lng']) && $_GET['lng']!=""){$lng = $_GET['lng'];}
if(isset($_GET['radius']) && $_GET['radius']!=""){$radius = $_GET['radius'];}

// Search the rows in the markers table
$query = sprintf("SELECT id, name, address, lat, lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '%s' ORDER BY distance ",
  $conn->real_escape_string($lat),
  $conn->real_escape_string($lng),
  $conn->real_escape_string($lat),
  $conn->real_escape_string($radius));

$result = $conn->query($query);

$markers=""; 
while($r = mysqli_fetch_assoc($result)) {
    $markers.= $r['id'].",";
}

$markers = substr($markers,0, -1);


if ($q!="") {
	
	$sorgu = "SELECT DISTINCT(products.stock_code),products.id,product_name,img,remote_img,remote_link from prices 
	right JOIN products on products.stock_code=prices.stock_code  and products.product_name like '%".$q."%'
	where prices.marker_id in (".$markers.")";

	$result = $conn->query($sorgu);
}


$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
echo json_encode($rows);


?>

