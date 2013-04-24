<?php
include_once('CoordFactory.php');
include_once('TripFactory.php');
include_once('UserFactory.php');
include_once('Util.php');

ob_start('ob_gzhandler');
//Util::log( "t: {$_POST['t']} and q: {$_POST['q']}" );


/*
Util::log ( "++++ HTTP Headers ++++" );
$headers = array();
foreach($_SERVER as $key => $value) {
    if (substr($key, 0, 5) <> 'HTTP_') {
        continue;
    }
    $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
    
    Util::log ( "{$header}: {$value}" );
    //$headers[$header] = $value;
}

$post = "";
foreach ($_POST as $key => $value){
        $post .= "$key => $value";        
        }
Util::log ( "POST THING {$post}");
*/

$t = isset( $_POST['t'] ) ? $_POST['t'] : null; 
$q = isset( $_POST['q'] ) ? $_POST['q'] : null; 
$d = isset( $_POST['d'] ) ? $_POST['d'] : null; 

if($t=="get_coords_by_trip"){	
	Util::log("");
	Util::log( "+++++++++++++ Download: Download Trip Coords ++++++++++");
	$obj = new CoordFactory();	
	echo $obj->getAllCoordsByTrip($q);
} else if ($t=="get_user_and_trips"){
	Util::log("");
	Util::log( "+++++++++++++ Download: Download User and Trips ++++++++++");
	$userFactory = new UserFactory();
	$tripFactory = new TripFactory();
	
	$response = array();
	$response['user'] = $userFactory->getUserByDevice($d);
	$response['trips'] = $tripFactory->getTripsByUser($response['user']->id);
	//Util::log("user id: ". $response['user']->id );
	//echo $response['user']->id;
	echo json_encode($response);
} else {
	header("HTTP/1.1 500 Internal Server Error");
	$response = new stdClass;
	$response->status = 'error';
	echo json_encode( $response );
}
Util::log( "+++++++++++++ Download: Complete ++++++++++");
?>