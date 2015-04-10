<?php

require_once "DataObject.class.php";
require_once "Book.class.php";
require_once "Publisher.class.php";

class Edition extends DataObject 
{
	public static function get($isbn) 
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_EDITION . ' WHERE isbn = :isbn';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $isbn, PDO::PARAM_STR);
			$st-> execute();
			$edition = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $edition ) 
			{			
				// Για τους muggles εκεί έξω ...
				// Όποιος δεν καταλαβαίνει τις παρακάτω γραμμές να με ρωτήσει ...
				// Είναι πολύ μαγικές και άμα τις τεκμηρίωσω 
				// η μαγεία τους θα χαθεί :P
				$edition['book'] = Book::get($edition["book_id"]);
				$edition['publisher'] = Publisher::get($edition["publisher_id"]);
				return new Edition( $edition );
			}				
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByBook(Book & $book)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_EDITION . ' WHERE book_id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $book->getValue("id"), PDO::PARAM_INT);
			$st-> execute();
			$rows = $st-> fetchAll();
			parent::disconnect( $conn );
						
			if ( $rows )
			{	
				$editions = array();
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				foreach ($rows as $row)
				{
					// Από την στιγμή που χρησιμοποιούμε το ίδιο το βιβλίο
					// για να αναζητήσουμε την έκδοση δεν χρειάζετε να ψάξουμε και το
					// βιβλίο. Απευθείας το περνώ ως παράμετρο για να αποθηκευτεί.
					// Άμα λειτουργεί σωστά και η php ως αντικειμενοστρεφή γλώσσα θεωρητικά
					// (νομίζω) ότι δεν δημιουργείται και καινούργιο αντικείμενο
					// οπότε γλειτόνουμε και χώρο στην μνήμη.
					
					// http://stackoverflow.com/questions/9331519/how-do-you-pass-objects-by-reference-in-php-5
					// Όποιος καταλαβαίνει τί συμβαίνει ας εξηγήσει και σε μένα. 
					$row['book'] = $book;
					$row['publisher'] = Publisher::get($row["publisher_id"]);
					array_push($editions, new Edition($row));
				}
				
				return $editions;
			}
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByPublisher(Publisher & $publisher)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_EDITION . ' WHERE publisher_id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $publisher->getValue("id"), PDO::PARAM_INT);
			$st-> execute();
			$rows = $st-> fetchAll();
			parent::disconnect( $conn );
	
			if ( $rows )
			{
				$editions = array();
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				foreach ($rows as $row)
				{
					// Από την στιγμή που χρησιμοποιούμε το ίδιο το βιβλίο
					// για να αναζητήσουμε την έκδοση δεν χρειάζετε να ψάξουμε και το
					// βιβλίο. Απευθείας το περνώ ως παράμετρο για να αποθηκευτεί.
					// Άμα λειτουργεί σωστά και η php ως αντικειμενοστρεφή γλώσσα θεωρητικά
					// (νομίζω) ότι δεν δημιουργείται και καινούργιο αντικείμενο
					// οπότε γλειτόνουμε και χώρο στην μνήμη.
						
					// http://stackoverflow.com/questions/9331519/how-do-you-pass-objects-by-reference-in-php-5
					// Όποιος καταλαβαίνει τί συμβαίνει ας εξηγήσει και σε μένα.
					$row['book'] = Book::get($row["book_id"]);
					$row['publisher'] = $publisher;
					array_push($editions, new Edition($row));
				}
	
				return $editions;
			}
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function add($isbn,$book_id,$publisher_id,$edition,$date,$language)
	{
		
		$conn = parent::connect();
		$sql = 'INSERT INTO '. TABLE_EDITION . ' (isbn,book_id,publisher_id,
				edition,date,language)	VALUES(:isbn,:book_id,:pusblisher_id,
				:edition,:date,:language)';
		
		try
		{
			// Κανονικά πρέπει να μπουν έλεγχοι για να μην σκάει όταν περνάνε
			// λάθος ορίσματα. Όποιος έχει όρεξη ας το κάνει.
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $isbn, PDO::PARAM_STR );
			$st-> bindValue( ":book_id", $book_id, PDO::PARAM_INT );
			$st-> bindValue( ":pusblisher_id", $publisher_id, PDO::PARAM_INT );
			$st-> bindValue( ":edition", $edition, PDO::PARAM_INT );
			$st-> bindValue( ":date", $date, PDO::PARAM_STR );
			$st-> bindValue( ":language", $language, PDO::PARAM_STR );
			$st-> execute();
			
			parent::disconnect( $conn );
			
			return true;
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
			return false;
		}
	}
	
	public function update($book_id,$publisher_id,$edition,$date,$language)
	{
		$conn = parent::connect();
		$sql = 'UPDATE '. TABLE_EDITION . ' SET book_id = :book_id,
				   publisher_id = :pusblisher_id, edition = :edition,
				   date = :date, language = :language WHERE isbn = :isbn';
		
		try
		{
			// Κανονικά πρέπει να μπουν έλεγχοι για να μην σκάει όταν περνάνε
			// λάθος ορίσματα. Όποιος έχει όρεξη ας το κάνει.
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $this->data['isbn'], PDO::PARAM_STR );
			$st-> bindValue( ":book_id", $book_id, PDO::PARAM_INT );
			$st-> bindValue( ":pusblisher_id", $publisher_id, PDO::PARAM_INT );
			$st-> bindValue( ":edition", $edition, PDO::PARAM_INT );
			$st-> bindValue( ":date", $date, PDO::PARAM_STR );
			$st-> bindValue( ":language", $language, PDO::PARAM_STR );
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
		$sql = 'DELETE FROM ' . TABLE_EDITION . ' WHERE isbn = :isbn';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":isbn", $this->data['isbn'], PDO::PARAM_STR);
			$st-> execute();
			parent::disconnect( $conn );
	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function checkAvailability()
	{
		//TODO
	}
	
	public function getBookId()
	{
		return $this->data['book']->getValue("id");
	}
	
	public function getPublishersId()
	{
		return $this->data['publisher']->getValue("id");
	}
	
	public function getPublishersString()
	{	
		return $this->data['publisher']->getValue("name");
	}
	
	public function getBookTitleString()
	{
		return $this->data['book']->getValue("title");
	}
	
	public function getBookDescriptionString()
	{
		return $this->data['book']->getValue("description");
	}
		
	protected $data = array(
			"book" => "",
			"isbn" => "",
			"publisher" => "",
			"edition" => "",
			"date" => "",
			"language" => ""
	);
	
}

?>
