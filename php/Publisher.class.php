<?php

require_once "DataObject.class.php";

/**
 * Defining the name of the table that is used to store the publishers
 */
define('TABLE_PUBLISHER', 'publishers');

class Publisher extends DataObject 
{
	public static function get($id) 
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_PUBLISHER . ' WHERE id = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $row ) 
				return new Publisher( $row );
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function add($name)
	{					
		$conn = parent::connect();
		$sql = 'INSERT INTO ' . TABLE_PUBLISHER . ' (name) VALUES(:name)';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":name", $name, PDO::PARAM_STR );
			$st-> execute();
			parent::disconnect( $conn );

		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function delete($id)
	{
		$conn = parent::connect();
		$sql = 'DELETE FROM ' . TABLE_PUBLISHER . ' WHERE id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			parent::disconnect( $conn );
	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}	
	
	protected $data = array(
			"id" => "",
			"name" => "",
	);
	
}

?>
