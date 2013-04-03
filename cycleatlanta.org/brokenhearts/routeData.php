<?php
include_once('CoordFactory.php');
include_once('TripFactory.php');
/*
include_once('CoordFactory_dev.php');
include_once('TripFactory_dev.php');
*/

if($_POST['t']=="get_coords_by_trip"){	
	$obj = new CoordFactory();	
	echo $obj->getCoordsByTrip($_POST['q']);
} else if ($_POST['t']=="get_trip_ids"){
	$obj = new TripFactory();
	echo $obj->getTripIdsByNotes($_POST['q']);	
} else {
	//no-op
}

?>