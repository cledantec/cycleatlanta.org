<?php

require_once('Database.php');
require_once('Coord.php');

class CoordFactory
{
	static $class = 'Coord';

	public static function insert( $trip_id, $recorded, $latitude, $longitude, $altitude=0, $speed=0, $hAccuracy=0, $vAccuracy=0 )
	{
		$db = DatabaseConnectionFactory::getConnection();

		$query = "INSERT INTO coord ( trip_id, recorded, latitude, longitude, altitude, speed, hAccuracy, vAccuracy ) VALUES ( '" .
				$db->escape_string( $trip_id ) . "', '" .
				$db->escape_string( $recorded ) . "', '" .
				$db->escape_string( $latitude ) . "', '" .
				$db->escape_string( $longitude ) . "', '" .
				$db->escape_string( $altitude ) . "', '" .
				$db->escape_string( $speed ) . "', '" .
				$db->escape_string( $hAccuracy ) . "', '" .
				$db->escape_string( $vAccuracy ) . "' )";

		if ( $db->query( $query ) === true )
		{
			//Util::log( __METHOD__ . "() added coord ( {$latitude}, {$longitude} ) to trip $trip_id" );
			return true;
		}
		else
			Util::log( __METHOD__ . "() ERROR failed to add coord ( {$latitude}, {$longitude} ) to trip $trip_id" );

		return false;
	}

	// trip_id can be a single id, or an array of ids
	// if it's an array of ids, returns the result object directly because creating an
	// array of hundreds of thousands of Coord objects is memory-intensive and not useful
	public static function getCoordsByTrip( $trip_id )
	{
		$db = DatabaseConnectionFactory::getConnection();
		$coords = array();
		$skipTrips = array();
		$latLongMinThreshold = .007; //delta must be more than 7m
		$latLongMaxThreshold = .1; //delta must be less than 200m (or whole trip is ignored).
		$query = "SELECT * FROM coord WHERE ";
	    if (is_array($trip_id)) {
	      $first = True;
	  		foreach ($trip_id as $idx => $single_trip_id ) {
	        	if ($first) {
					$first = False;
				} else {
					$query .= " OR ";
				}
				$query .= "trip_id='" . $db->escape_string($single_trip_id) . "'";
			}
		} else {
			$query .= "trip_id IN (" . $db->escape_string( $trip_id ) . ")";
		}
		//$query .= " ORDER BY trip_id ASC, recorded ASC";
		Util::log( __METHOD__ . "() with query of length " . strlen($query) . 
			': memory_usage = ' . memory_get_usage(True));
		
		if ( ( $result = $db->query( $query ) ) && $result->num_rows )
		{
		  Util::log( __METHOD__ . "() with query of length " . strlen($query) . 
				' returned ' . $result->num_rows .' rows: memory_usage = ' . memory_get_usage(True));

			// if the request was for an array of trip_ids then just return the $result class
			// (I know, this is not very OO but putting it all in a structure in memory is no good either
			if (is_array($trip_id)) {
				return $result;			
			}
			$skipTrip = null;
			$last = null;
			while ( $coord = $result->fetch_object( self::$class ) ){
				//test for same trip
				if($skipTrip && $coord->trip_id == $skipTrip->trip_id){
					//no-op, this is a trip to skip
				}else{
					$skipTrip = null;
					if($last && $coord->trip_id == $last->trip_id){
						if( Util::latlongPointDistance($last->latitude, $last->longitude, $coord->latitude, $coord->longitude) >= $latLongMinThreshold ){
							$coords[] = $coord;
						}
						if( Util::latlongPointDistance($last->latitude, $last->longitude, $coord->latitude, $coord->longitude) >= $latLongMaxThreshold ){
							$skipTrip = $coord;
							$skipTrips = $coord->trip_id;
							for($i=count($coords)-1;$i >=0 ; $i--){
								//start at the end of the array, remove items until we get to the previous trip
								if($coords[$i]->trip_id == $skipTrip->trip_id){
									array_pop($coords);	
								}else{
									break;
								}
							}
						}	
					}else{ 
						$coords[] = $coord;						
					}
				}
				$last = $coord;
			}

			$result->close();
		}
		$result = null;
		Util::log( __METHOD__ . "() with query " . $query . " of length " . strlen($query) . 
			' RET2: memory_usage = ' . memory_get_usage(True));
		Util::log("Trips to skip ".count($skipTrips));


		return json_encode($coords);
	}
}
