<?php

require_once('Database_dev.php');
require_once('Note.php');

define ('IMAGE_PATH', '../uploads/');

class NoteFactory
{
	static $class = 'Note';

	public static function insert( $user_id, $recorded, $latitude, $longitude, $altitude=0, $speed=0, $hAccuracy=0, $vAccuracy=0, $note_type, $details, $image_url, $image_file )
	{
		$db = DatabaseConnectionFactory::getConnection();
//need to create image_url string before uploading here
		$query = "INSERT INTO note ( user_id, recorded, latitude, longitude, altitude, speed, hAccuracy, vAccuracy, note_type, details, image_url ) VALUES ( '" .
				$db->escape_string( $user_id ) . "', '" .
				$db->escape_string( $recorded ) . "', '" .
				$db->escape_string( $latitude ) . "', '" .
				$db->escape_string( $longitude ) . "', '" .
				$db->escape_string( $altitude ) . "', '" .
				$db->escape_string( $speed ) . "', '" .
				$db->escape_string( $hAccuracy ) . "', '" .
				$db->escape_string( $vAccuracy ) . "', '" .
				$db->escape_string( $note_type ) . "', '" .
				$db->escape_string( $details ) . "', '" .
				$db->escape_string( $image_url ) . "' )";

		if ( $db->query( $query ) === true &&
			 ( $id = $db->insert_id ) )
		{
			Util::log( __METHOD__ . "() added note for user {$user_id}, at ( {$latitude}, {$longitude} ), on {$recorded}, type {$note_type}, details {$details}" );
			
			//save the image
			Util::log (getcwd()); 
			if ($image_file != "<>"){
				if (file_put_contents(IMAGE_PATH . $image_url . '.jpg', $image_file)) {
					// Move succeed.
					Util::log ("Image saved to ". IMAGE_PATH . $image_url . "jpg");
				} else {
				    // Move failed. Possible duplicate?
					Util::log ("WARNING: Image not saved ". IMAGE_PATH . $image_url . ".jpg");
				}
			}
			return self::getNote( $id );
		}
		else
			Util::log( __METHOD__ . "() ERROR failed to added flagged location for user {$user_id}, at ( {$latitude}, {$longitude} ), on {$recorded}, type {$note_type}, details {$details}" );

		return false;
	}
	
	public static function getNote( $id )
	{
		$db = DatabaseConnectionFactory::getConnection();
		$trip = null;

		if ( ( $result = $db->query( "SELECT * FROM note WHERE id='" . $db->escape_string( $id ) . "'" ) ) &&
				( $result->num_rows ) )
		{
			$trip = $result->fetch_object( self::$class );
			$result->close();
		}

		return $trip;
	}
	
	public static function getNoteByUserStart( $user_id, $recorded )
	{
		$db = DatabaseConnectionFactory::getConnection();
		$note = null;

		$query = "SELECT * FROM note WHERE user_id='" . $db->escape_string( $user_id ) . "' AND " .
				 "recorded='" . $db->escape_string( $recorded ) . "'";

		if ( ( $result = $db->query( $query ) ) &&
				( $result->num_rows ) )
		{
			$note = $result->fetch_object( self::$class );
			$result->close();
		}

		return $note;
	}


}