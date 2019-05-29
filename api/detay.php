<?php
header("Content-type: application/json; charset=utf-8");
include("conn.php");

$code = "";
$lat = 0;
$lng = 0;
$radius = 0;

if(isset($_GET['code']) && $_GET['code']!=""){$code = $_GET['code'];}
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


$sorgu = "SELECT markers.id,markers.brand,prices.stock_code,product_name,Min(prices.price) as price from prices 
right JOIN products on products.stock_code=prices.stock_code
right join markers on markers.id=prices.marker_id
where prices.marker_id in (".$markers.") and prices.stock_code='".$code."' GROUP BY markers.brand ORDER BY prices.price;
";

$result = $conn->query($sorgu);


$brand = array();
while($r = mysqli_fetch_assoc($result)) {
    $brand[] = $r;
}



$sorgu2 = "SELECT markers.brand,markers.name,markers.address,markers.lat,markers.lng,prices.price as price from prices 
right JOIN products on products.stock_code=prices.stock_code
right join markers on markers.id=prices.marker_id
where prices.marker_id in (".$markers.") and prices.stock_code='".$code."'  ORDER BY prices.price;
";

$result2 = $conn->query($sorgu2);


$detail = array();
while($r = mysqli_fetch_assoc($result2)) {
    $detail[] = $r;
}

$res=array();

$res = $brand;

foreach ($brand as $key => $b) {
	foreach ($detail as $key2 => $d) {
		if($b['brand']==$d['brand'])
		{
			$res[$key]['details'][] = $d;
		}
		
	}
}


echo json_encode($res);


?>

