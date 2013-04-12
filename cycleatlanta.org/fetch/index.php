<?php
include_once('CoordFactory.php');
include_once('TripFactory.php');
include_once('UserFactory.php');
include_once('Util.php');

ob_start('ob_gzhandler');
//Util::log( "t: {$_POST['t']} and q: {$_POST['q']}" );

if($_POST['t']=="get_coords_by_trip"){	
	$obj = new CoordFactory();	
	echo $obj->getCoordsByTrip($_POST['q']);
} else if ($_POST['t']=="get_user_and_trips"){
	$userFactory = new UserFactory();
	$tripFactory = new TripFactory();
	
	$response = array();
	$response['user'] = $userFactory->getUserByDevice($_POST['d']);
	$response['trips'] = $tripFactory->getTripsByUser($response['user']->id);
	
	
	echo json_encode($response);
	
} else {
	//no-op
}

?>