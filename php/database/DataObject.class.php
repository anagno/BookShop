<?php
require_once "config.php";

/**  
 * Ο κώδικας έχει παρθεί από το βιβλίο Beginning php 5.3 - Matt Doyle
 * Για λεπτομέρεις σελ. 389
 * 
 * DataObject is an abstract class from which you can derive 
 * classes to handle database access and data retrieval
 * @author anagno
 *
 */
abstract class DataObject 
{
	/**
	 * @var unknown  a protected $data array to hold the record ’ s data
	 */
	protected $data = array();
	
	/**
	 * The constructor accepts an associative array of field names 
	 * and values ( $data ) and stores them in the protected $data 
	 * array (assuming each field name exists in $data ). In this 
	 * way it ’ s possible for outside code to create fully populated 
	 * data objects.
	 * 
	 * @param unknown $data
	 */
	public function __construct( $data ) 
	{
		foreach ( $data as $key => $value ) 
		{
			if ( array_key_exists( $key, $this-> data ) ) $this-> data[$key] =	$value;
		}
	}
	
	/**
	 * The getValue() method accepts a field name, then looks up 
	 * that name in the object ’ s $data array. If found, it returns 
	 * its value. If the field name wasn ’ t found, the method halts 
	 * execution with an error message. getValue() enables outside code 
	 * to access the data stored in the object.
	 * 
	 * @param unknown $field
	 * @return unknown
	 */
	public function getValue( $field ) 
	{
		if ( array_key_exists( $field, $this-> data ) ) 
		{
			return $this-> data[$field];
		} 
		else 
		{
			die("Field not found");
		}
	}
	
	/**
	 * getValueEncoded() is a convenience method that allows outside 
	 * code to retrieve a field value that has been passed through 
	 * PHP ’ s htmlspecialchars() function
	 * 
	 * http://php.net/htmlspecialchars
	 * http://stackoverflow.com/questions/3129899/what-are-the-common-defenses-against-xss
	 * 
	 * 
	 * @param unknown $field
	 * @return string
	 */
	public function getValueEncoded( $field ) 
	{
		return htmlspecialchars( $this-> getValue( $field ) );
	}
	
	
	/**
	 * Create a PDO connection to the database
	 * 
	 * @return PDO
	 */
	protected function connect() 
	{
		try 
		{
			$conn = new PDO('mysql:host='.DB_SERVER.';dbname='.DB_DATABASE.';charset=utf8',DB_USERNAME,DB_PASSWORD);
			$conn-> setAttribute( PDO::ATTR_PERSISTENT, true );
			$conn-> setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} 
		catch ( PDOException $e ) 
		{
			die( "Connection failed:"  . $e-> getMessage() );
		}
		return $conn;
	}
	
	
	/**
	 * Destroy PDO connection to the database
	 * 
	 * @param unknown $conn
	 */
	protected function disconnect( $conn ) 
	{
		$conn = "";
	}
	
}

?>
