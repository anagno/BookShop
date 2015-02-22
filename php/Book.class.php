<?php

require_once "DataObject.class.php";

/**
 * Defining the name of the table that is used to store the books
 */
define('TABLE_BOOK', 'books');

class Book extends DataObject 
{
	public static function get($id) 
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_BOOK . ' WHERE id = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $row ) 
				return new Book( $row );
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function add($title,$description)
	{	
		$conn = parent::connect();
		$sql = 'INSERT INTO ' . TABLE_BOOK . ' (title,description) 
				           VALUES(:title,:description)';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":title", $title, PDO::PARAM_STR );
			$st-> bindValue( ":description", $description, PDO::PARAM_STR );
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
		$sql = 'DELETE FROM ' . TABLE_BOOK . ' WHERE id = :id';
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
			"title" => "",
			"description" => ""
	);
	
}

?>
