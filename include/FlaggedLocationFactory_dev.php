<?php

require_once('Database_dev.php');
require_once('FlaggedLocation.php');

class FlaggedLocationFactory
{
	static $class = 'FlaggedLocation';

	public static function insert( $trip_id, $user_id, $recorded, $latitude, $longitude, $altitude=0, $speed=0, $hAccuracy=0, $vAccuracy=0, $flag_type, $details, $image_url )
	{
		$db = DatabaseConnectionFactory::getConnection();
//need to create image_url string before uploading here
		$query = "INSERT INTO flagged_location ( trip_id, user_id, recorded, latitude, longitude, altitude, speed, hAccuracy, vAccuracy, flag_type, details, imgae_url ) VALUES ( '" .
				$db->escape_string( $trip_id ) . "', '" .
				$db->escape_string( $user_id ) . "', '" .
				$db->escape_string( $recorded ) . "', '" .
				$db->escape_string( $latitude ) . "', '" .
				$db->escape_string( $longitude ) . "', '" .
				$db->escape_string( $altitude ) . "', '" .
				$db->escape_string( $speed ) . "', '" .
				$db->escape_string( $hAccuracy ) . "', '" .
				$db->escape_string( $vAccuracy ) . "', '" .
				$db->escape_string( $flag_type ) . "', '" .
				$db->escape_string( $details ) . "', '" .
				$db->escape_string( $image_url ) . "' )";

		if ( $db->query( $query ) === true )
		{
			Util::log( __METHOD__ . "() added flagged location for user {$user_id}, at ( {$latitude}, {$longitude} ), on {$recorded}, type {$flag_type}, details {$details}, with trip {$trip_id}" );
			return true;
		}
		else
			Util::log( __METHOD__ . "() ERROR failed to added flagged location for user {$user_id}, at ( {$latitude}, {$longitude} ), on {$flag_recorded}, type {$flag_type}, details {$details}, with trip {$trip_id}" );

		return false;
	}
}
