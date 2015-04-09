<?php

require_once "DataObject.class.php";

class Author extends DataObject 
{
	
	// Μάλλον μια κακή συνάρτηση διότι θέλει πολύ ογκο δεδομένων.
	// Άμα σκεφτεί κανείς κανένα καλύτερο τρόπο ας τον εφαρμόσει αν δεν βαριέται.
	public static function getAllAuthros()
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_AUTHOR . ' ORDER BY name ASC';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> execute();
			$rows = $st-> fetchAll(PDO::FETCH_COLUMN);
			parent::disconnect( $conn );
				
			if ( $rows )
			{
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				$authors = array();
				
				foreach ($rows as $row)
				{
					array_push($authors, self::get($row) );
				}		
				
				return $authors;
			}	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}	
	}
	
	
	public static function get($id) 
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_AUTHOR . ' WHERE id = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );

			if ( $row ) 
				return new Author( $row );
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByBook($book_id)
	{
		$conn = parent::connect();
		$sql = 'SELECT author_id FROM ' . TABLE_WRITES . ' WHERE book_id = :book_id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":book_id", $book_id, PDO::PARAM_INT );
			$st-> execute();
			$rows = $st-> fetchAll(PDO::FETCH_COLUMN);
			parent::disconnect( $conn );
				
			if ( $rows )
			{
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				$authors = array();
				
				foreach ($rows as $row)
				{
					array_push($authors, self::get($row) );
				}		
				
				return $authors;
			}	
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
		$sql = 'INSERT INTO ' . TABLE_AUTHOR . ' (name) VALUES(:name)';
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
		$sql = 'DELETE FROM ' . TABLE_AUTHOR . ' WHERE id = :id';
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