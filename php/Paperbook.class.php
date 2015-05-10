<?php

require_once "DataObject.class.php";
require_once "Edition.class.php";
require_once "Book.class.php";
require_once "User.class.php";


class Paperbook extends DataObject
{
	
	public static function get($id)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_PAPERBOOK . ' WHERE id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_STR);
			$st-> execute();
			$paperbook = $st-> fetch();
			parent::disconnect( $conn );
				
			if ( $paperbook )
			{
				try 
				{
					$sub_sql = 'SELECT * FROM ' . TABLE_BORROWS . ' WHERE paperbook_id = :id ORDER BY start_period ASC';
					$conn = parent::connect();
					$st = $conn-> prepare( $sub_sql );
					$st-> bindValue( ":id", $id, PDO::PARAM_INT );
					$st-> execute();
					$borrows = $st ->fetchAll(PDO::FETCH_ASSOC);
					// Εδώ αποθηκεύεται για άλλη μια φορά το paperbook_id κάτι που σπαταλάει χώρο.
					// Αλλά βαριέμαι. Είναι 3.35 το πρωί και συνηδητοποιώ ότι προτιμώ να δω το 
					// jolene (http://www.imdb.com/title/tt0867334/) που παίζει στο STAR παρά να 
					// βάλω το μυαλουδάκι μου και να διορθώσει αυτό το πρόβλημα.
					// Ασχετο (όχι βέβαια ότι όλα τα άλλα που γράφω είναι σχετικά)
					$paperbook['borrows'] = $borrows;
					parent::disconnect( $conn );					
				}
				catch ( PDOException $e )
				{
						
					die( "Sub-query failed for borrows: " . $e->getMessage() );
				}
				$paperbook['edition']= Edition::get($paperbook['isbn']);
				
				return new Paperbook($paperbook);
			}
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByEdition(Edition & $edition)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_PAPERBOOK . ' WHERE isbn = :isbn';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $edition->getValue("isbn"), PDO::PARAM_INT);
			$st-> execute();
			$rows = $st-> fetchAll();
			parent::disconnect( $conn );
	
			if ( $rows )
			{
				$paperbooks = array();
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				foreach ($rows as $row)
				{
					// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
					// TODO: Συγκεκριμένα γίνεται σπατάλη μνήμης διότι δεν
					//       χρησιμοποιούμε το γεγονός ότι η έκδοση είναι παντού
					//       ίδια.
					array_push($paperbooks, self::get($row['id']));
				}
	
				return $paperbooks;
			}
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByUser(User & $user)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_BORROWS . ' WHERE username = :username AND end_period is NULL';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":username", $user-> getValueEncoded( "username" ), PDO::PARAM_STR);
			$st-> execute();
			$rows = $st-> fetchAll();
			parent::disconnect( $conn );
	
			if ( $rows )
			{
				$paperbooks = array();
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				foreach ($rows as $row)
				{
					// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
					array_push($paperbooks, self::get($row['paperbook_id']));
				}
	
				return $paperbooks;
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
			$sql = 'INSERT INTO ' . TABLE_PAPERBOOK . ' (isbn,binding,location)
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
	
	// Βαριέμαι να κάνω και σχόλια... Όποιος δεν καταλαβαίνει ας προσευχηθεί στον θεό...
	// Είναι ο μόνος που καταλαβαίνει πλεόν αυτόν τον κώδικα.
	public function update($isbn,$binding,$location)
	{
		$conn = parent::connect();
		$sql = 'UPDATE '. TABLE_PAPERBOOK . ' SET isbn = :isbn,
				   binding = :binding, location = :location
				   WHERE id = :id';
		
		try
		{
			// Κανονικά πρέπει να μπουν έλεγχοι για να μην σκάει όταν περνάνε
			// λάθος ορίσματα. Όποιος έχει όρεξη ας το κάνει.
			// Και πρέπει να σταματήσω να αντιγράφω τα σχόλια μαζί με τον κώδικα...
			// Καρφόνομαι ...
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
			$st-> bindValue( ":isbn", $isbn, PDO::PARAM_STR );
			$st-> bindValue( ":binding", $binding, PDO::PARAM_STR );
			$st-> bindValue( ":location", $location, PDO::PARAM_STR );
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
		$sql = 'DELETE FROM ' . TABLE_PAPERBOOK . ' WHERE id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT);
			$st-> execute();
			parent::disconnect( $conn );
	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function bookRent($user)
	{
		if(!self::isBorrowed())
		{
			$conn = parent::connect();
			$sql = 'INSERT '. TABLE_BORROWS . ' (paperbook_id,username,start_period,
					end_period) VALUES(:id,:username,NOW(), null)';		
			try
			{
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
				$st-> bindValue( ":username", $user, PDO::PARAM_STR );
				$st-> execute();
			
				parent::disconnect( $conn );
			}
			catch ( PDOException $e )
			{
				parent::disconnect( $conn );
				die( "Query failed: " . $e->getMessage() );
			}			
		}
	}
	
	public function bookReturn()
	{
		$conn = parent::connect();
		$sql = 'UPDATE '. TABLE_BORROWS . ' SET end_period = NOW()
				   WHERE paperbook_id = :id';
		
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
	
	public function isBorrowed()
	{
		$last_borrow = end($this->data['borrows']);
		
		if($last_borrow['end_period'] === NULL &&
			count($this->data['borrows'])>0 )
		{
			return true;
		}
		else
		{ 
			return false;
		}
	}
	
	public function lastStartBorrowDate()
	{
		$last_borrow = end($this->data['borrows']);
	
		return $last_borrow['start_period'];
	}
	
	public function getBookTitleString()
	{
		return $this->data['edition']->getBookTitleString();
	}
	
	public function getIsbn()
	{
		return $this->data['edition']->getValue("isbn");
	}
	
	
	public function getBookId()
	{
		return $this->data['edition']->getBookId();
	}
	
	
	protected $data = array(
			"id" => "",
			"edition" => "",
			"binding" => "",
			"location" => "",
			"borrows" => array()
	);
	
}