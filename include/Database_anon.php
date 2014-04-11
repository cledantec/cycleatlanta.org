<?php

require_once( 'Util.php' );

abstract class DatabaseConnection extends mysqli
{
	const USER     = 'govathon13db';
	const PASSWORD = '7Jk3WYNt';
	const DATABASE = 'catl_govathon';

	public function __construct( $host, $user, $password, $database )
	{
		parent::__construct( $host, $user, $password, $database );

		if ( mysqli_connect_error() )
			throw new DatabaseConnectionException();
	}

	public function query( $query )
	{
		if ( !($result = parent::query( $query ) ) )
			Util::log( __METHOD__ . "() ERROR {$this->errno}: {$this->error}: \"{$query}\"" );
		
		return $result;
	}
}

class LocalDatabaseConnection extends DatabaseConnection 
{
	const HOST     = 'mysql.govathon.cycleatlanta.org';

	public function __construct()
	{
		parent::__construct( self::HOST, self::USER, self::PASSWORD, self::DATABASE );
	}
}

class DatabaseConnectionFactory 
{
	static protected $connection = null;

	public static function getConnection()
	{
		if ( self::$connection )
			return self::$connection;
		else
			return self::$connection = new LocalDatabaseConnection();
	}
}

class DatabaseException extends Exception
{
	public function __construct( $message, $code )
	{
		parent::__construct( $message, $code );
	}
}

class DatabaseConnectionException extends DatabaseException
{
	public function __construct( $message=null, $code=null )
	{
		if ( !$message )
			mysqli_connect_error();

		if ( !$code )
			mysqli_connect_errno();

		parent::__construct( mysqli_connect_error(), mysqli_connect_errno() );
	}
}

