<?php

require_once "DataObject.class.php";

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
	
	// Μάλλον μια κακή συνάρτηση διότι θέλει πολύ ογκο δεδομένων.
	// Άμα σκεφτεί κανείς κανένα καλύτερο τρόπο ας τον εφαρμόσει αν δεν βαριέται.
	public static function getAllPublishers()
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_PUBLISHER . ' ORDER BY name ASC';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> execute();
			$rows = $st-> fetchAll(PDO::FETCH_COLUMN);
			parent::disconnect( $conn );
	
			if ( $rows )
			{
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				$publishers = array();
	
				foreach ($rows as $row)
				{
					array_push($publishers, self::get($row) );
				}
	
				return $publishers;
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
		$sql = 'INSERT INTO ' . TABLE_PUBLISHER . ' (name) VALUES(:name)';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":name", $name, PDO::PARAM_STR );
			$st-> execute();
			
			$new_publisher_id = $conn->lastInsertId();
			parent::disconnect( $conn );
			
			return $new_publisher_id;

		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function update($name)
	{
		$conn = parent::connect();
		$sql = 'UPDATE '. TABLE_PUBLISHER . ' SET name = :name
						WHERE id = :id';
		try
		{
			// Κανονικά πρέπει να μπουν έλεγχοι για να μην σκάει όταν περνάνε
			// λάθος ορίσματα. Όποιος έχει όρεξη ας το κάνει.
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
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
	
	public function delete()
	{
		$conn = parent::connect();
		$sql = 'DELETE FROM ' . TABLE_PUBLISHER . ' WHERE id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
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
