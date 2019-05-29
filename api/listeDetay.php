<?php
header("Content-type: application/json; charset=utf-8");
require("conn.php");
 
// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"]; 
$radius = $_GET["radius"];
$markersIds = "";
$groupby="";
if(isset($_GET["markersIds"]) and $_GET["markersIds"]!=""){
	$markersIds = " and id in (".$_GET["markersIds"].")";
	$groupby = "GROUP BY brand";

  $markersArray = explode(",",$_GET["markersIds"] );
  $stockCodesArray = explode(",",$_GET["stockCodes"] );
}


// Search the rows in the markers table
$query = sprintf("SELECT id, brand, name, address, lat, lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markers %s HAVING distance < '%s' %s  ORDER BY distance ",
  $conn->real_escape_string($center_lat),
  $conn->real_escape_string($center_lng),
  $conn->real_escape_string($center_lat),
  $conn->real_escape_string($groupby),
  $conn->real_escape_string($radius),
  $conn->real_escape_string($markersIds));

  $result = $conn->query($query);


if (!$result) {
  die("Invalid query: " . mysqli_error());
}


$brands = array([
"id"=>"0",
"brand" => "Tümü"
]);
while($r = mysqli_fetch_assoc($result)) {
    $brands[] = $r;
}

$totalTumu = 0;

 $prices = array();
foreach ($markersArray as $key => $m) {
  $s = "SELECT * from prices 
        LEFT JOIN products on prices.stock_code=products.stock_code 
        where marker_id='".$m."' and prices.stock_code='".$stockCodesArray[$key]."' ";
  $result = $conn->query($s);


  while($r = mysqli_fetch_assoc($result)) {
      $prices[] = $r;
      $brands[0]['details'][] = $r;
      $totalTumu  += floatval($r['price']);
      $brands[0]['total'] = (string)$totalTumu;
  }

}


$res=array();

$res = $brands;

foreach ($brands as $key => $b) {
  $total=0;
  foreach ($prices as $key2 => $p) {
    
    if($b['id']==$p['marker_id'])
    {
        $res[$key]['details'][] = $p;
        $total  += floatval($p['price']);
        $res[$key]['total'] = (string)$total;
    }

  }
}




echo json_encode($res);


?>