<?php

require_once "DataObject.class.php";


class Paperbook extends DataObject
{
	
	public static function get($id)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_PAPERBOOk . ' WHERE id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_STR);
			$st-> execute();
			$paperbook = $st-> fetch();
			parent::disconnect( $conn );
				
			if ( $paperbook )
			{
				$paperbook['isbn']= Edition::get($paperbook['isbn']);
				
				return new Paperbook($paperbook);
			}
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function add($isbn,$binding,$location)
	{
		$conn = parent::connect();
		
		try
		{
			// Κανονικά πρέπει να ελέγχουμε τις τιμές που παρέχονται διότι
			// θα σκάνε τα ερωτήματα αν είναι μη έγκυρες οι τιμές.
			// Όποιος έχει όρεξη ας το κάνει... Εγώ πάω να σουβλίσω το αρνί ...
			$sql = 'INSERT INTO ' . TABLE_PAPERBOOk . ' (isbn,binding,location)
				           VALUES(:isbn,:binding,:location)';
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $isbn, PDO::PARAM_STR );
			$st-> bindValue( ":binding", $binding, PDO::PARAM_STR );
			$st-> bindValue( ":location", $location, PDO::PARAM_STR );
			$st-> execute();
			
			$new_paperbook_id = $conn->lastInsertId();
			
			parent::disconnect( $conn );
			
			return $new_paperbook_id;
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
		
	}
	
	
	protected $data = array(
			"id" => "",
			"edition" => "",
			"binding" => "",
			"location" => ""
	);
	
}