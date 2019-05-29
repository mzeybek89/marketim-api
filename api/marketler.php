<?php
header("Content-type: application/json; charset=utf-8");
require("conn.php");
 
// Get parameters from URL
$center_lat = $_GET["lat"];
$center_lng = $_GET["lng"]; 
$radius = $_GET["radius"];


// Search the rows in the markers table
$query = sprintf("SELECT id, name, address, lat, lng, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM markers HAVING distance < '%s' ORDER BY distance ",
  $conn->real_escape_string($center_lat),
  $conn->real_escape_string($center_lng),
  $conn->real_escape_string($center_lat),
  $conn->real_escape_string($radius));

  $result = $conn->query($query);


if (!$result) {
  die("Invalid query: " . mysqli_error());
}


$rows = array();
while($r = mysqli_fetch_assoc($result)) {
    $rows[] = $r;
}
echo json_encode($rows);


?>