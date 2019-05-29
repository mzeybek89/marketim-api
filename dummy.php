<?
header("Content-type: application/json; charset=utf-8");
require("api/conn.php");
ini_set('max_execution_time', 0); // for infinite time of execution 


$markers  = $conn->query("SELECT id from markers where id in(21) ");

$products = $conn->query("SELECT stock_code from products limit 100");


$rows_marker = array();
while($r = mysqli_fetch_assoc($markers)) {
    $rows_marker[] = $r;
}

$rows_product = array();
while($r = mysqli_fetch_assoc($products)) {
    $rows_product[] = $r;
}

//$conn->query("TRUNCATE TABLE prices");

$prices = array();
$i=0;
foreach ($rows_marker as $key => $val) {
	foreach ($rows_product as $key2 => $val2) {
		$randomFloat = rand(0, 1000) / 10;
		$price = number_format($randomFloat,2);	
		$prices[$i]['marker_id'] = $val['id'];
		$prices[$i]['stock_code'] = $val2['stock_code'];
		$prices[$i]['price'] = $price;
		$prices[$i]['update_date'] = date('Y-m-d H:i:s');

		$q="insert into prices (marker_id,stock_code,price,update_time) values ('".$val['id']."','".$val2['stock_code']."','".$price."','".date('Y-m-d H:i:s')."')";

		$conn->query($q);

		$i++;
	}
}


echo "ok";


?>