<?php

/**
* @desc collect static utility functions here
*/
class Util
{
	const DEV_HOSTNAME  = '';
	const PROD_HOSTNAME = '';

	protected static $hostname = null;

	public static function log( $data )
	{
		if ( is_string( $data ) )
		{
			if ( defined( '__SCRIPT__' ) )
				$data = __SCRIPT__ . " {$data}";

			error_log( $data );
		}
		elseif ( is_object( $data ) && $data instanceof Exception )
			error_log( $data->__toString() );
		else
			error_log( var_export( $data, true ) );
	}

	/**
	 * @desc produces a case-sensitive URL-safe base64-encoded hash
	 * NOTE: throws away any '=' characters for padding;
	 *       which results in an un-decodeable string.
	 * @param binary $data
	 */
	public static function hash64( $data )
	{
		$base64	= base64_encode( $data );
		$replace = array (
				'+' => '-',
				'/' => '_',
				'=' => '',
				);
		//$hash64 = strtr( $base64, '+/=', '-_.' );
		$hash64 = strtr( $base64, $replace );
		return $hash64;
	}

	/**
	 * @desc produces a case-sensitive URL-safe base64-encoded hash
	 * @param string $url
	 */
	public static function hashURL( $url )
	{
		return self::hash64( md5( $url, true ) );
	}

	/**
	 * @desc decode a URL-safe base64 encoded data
	 */
	public static function safe64_decode( $safe64 )
	{
		$base64	= strtr( $safe64, '-_.', '+/=' );
		$data	= base64_decode( $base64 );
		return $data;
	}

	/**
	 * @desc encode a URL-safe base64 encoded data
	 */
	public static function safe64_encode( $data )
	{
		$base64	= base64_encode( $data );
		$safe64 = strtr( $base64, '+/=', '-_.' );
		return $safe64;
	}

	public static function baseURL()
	{
		if ( self::httpHost() != self::serverAddr() )
			$base = self::httpHost();
		else
			$base = self::serverName();

		return "http://{$base}/";
	}

	public static function isDev()
	{
		return ( self::hostName() === self::DEV_HOSTNAME );
	}

	public static function isProd()
	{
		return ( self::hostName() === self::PROD_HOSTNAME );
	}

	public static function hostName()
	{
		if ( !isset( self::$hostname ) )
			self::$hostname = trim( `/bin/hostname` );

		return self::$hostname; 
	}

	public static function httpHost()
	{
		return $_SERVER['HTTP_HOST'];
	}

	public static function serverAddr()
	{
		return $_SERVER['SERVER_ADDR'];
	}

	public static function serverName()
	{
		return $_SERVER['SERVER_NAME'];
	}

}

