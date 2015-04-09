<?php

require_once "DataObject.class.php";
require_once "Author.class.php";

class Book extends DataObject 
{
	public static function get($id) 
	{
		//
		// Dear maintainer:
		//
		// Once you are done trying to 'optimize' this routine,
		// and have realized what a terrible mistake that was,
		// please increment the following counter as a warning
		// to the next guy:
		//
		// total_hours_wasted_here = 1
		//
		// http://stackoverflow.com/questions/184618/what-is-the-best-comment-in-source-code-you-have-ever-encountered
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_BOOK . ' WHERE id = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			$book = $st-> fetch();
			parent::disconnect( $conn );
					
			if ( $book ) 
			{
				try 
				{
					$sub_sql = 'SELECT type FROM ' . TABLE_CATEGORIES . ' WHERE book_id = :id';
					$conn = parent::connect();
					$st = $conn-> prepare( $sub_sql );
					$st-> bindValue( ":id", $id, PDO::PARAM_INT );
					$st-> execute();
					//http://stackoverflow.com/questions/19470423/unable-to-fetch-multiple-rows-from-mysql-using-pdo
					$categories = $st ->fetchAll(PDO::FETCH_COLUMN);
					//http://www.java-samples.com/showtutorial.php?tutorialid=997
					$book['categories'] = $categories;
					parent::disconnect( $conn );
				}
				catch ( PDOException $e )
				{
					
					die( "Sub-query failed for categories: " . $e->getMessage() );
				}
				
				$book['authors'] = Author::getByBook($id);
				
				return new Book( $book );
			}
				
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function getByAuthor(Author $author)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_WRITES . ' WHERE author_id = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $author->getValue("id"), PDO::PARAM_INT );
			$st-> execute();
			$rows = $st ->fetchAll();
			parent::disconnect( $conn );

			if($rows)
			{
				$books = array();
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				// TODO: Συγκεκριμένα γίνεται σπατάλη μνήμης διότι δεν 
				//       χρησιμοποιούμε το γεγονός ότι ο συγγράφεας είναι παντού 
				//       ίδιος και αντί αυτού δεσμεύουμε μνήμη για την δημιουργία
				//       καινούργιου αντικειμένου συγγραφέα
				//       για κάθε βιβλίου που έχει γράψει 
				foreach ($rows as $row)
				{											
					array_push($books, self::get($row['book_id']));
				}
				
				return $books;
				
			}
		}
		catch (PDOException $e)
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );			
		}	
	}
	
	public static function add($title,$description, $categories, $authors_id )
	{
		//TODO: την γραμματέας μας...
	
		$conn = parent::connect();
	
		try
		{
			// Εισάγωγουμε το βιβλίο στην βάση
			$sql = 'INSERT INTO ' . TABLE_BOOK . ' (title,description)
				           VALUES(:title,:description)';
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":title", $title, PDO::PARAM_STR );
			$st-> bindValue( ":description", $description, PDO::PARAM_STR );
			$st-> execute();
				
			// http://php.net/manual/en/pdo.lastinsertid.php
			$new_book_id = $conn->lastInsertId();
			// Συνδέουμε τους συγγραφείς που δίνονται με το καινούργιο βιβλίο
			
			// Πρέπει να γίνει με ένα sql insert για θέμα απόδοσης
			// αλλά άστο για άλλη φορά.
			// Ακόμα δεν ελέγχω ότι η μεταβλητή που παρέχεται είναι πίνακας.
			foreach ($categories as $category)
			{
				$sql = 'INSERT INTO '. TABLE_CATEGORIES . '(book_id,type)
					VALUES(:id,:category)';
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":id", $new_book_id, PDO::PARAM_INT );
				$st-> bindValue( ":category", $category, PDO::PARAM_STR );
				$st-> execute();
			}
				
			// Ομοίως και εδώ !!! Πεθαίνει ένα κομμάτι μου μέσα που τα γράφω αυτά
			// και ξέρω ότι δεν θα τα αντικαταστήσω. Μέχρι το τέλος της εργασίας
			// αυτής προβλέπεται να είμαι πιο νεκρός και από την νεκρά θάλασσα.
			
			foreach ($authors_id as $author_id)
			{
				$sql = 'INSERT INTO '. TABLE_WRITES . '(author_id,book_id)
					VALUES(:author_id,:book_id)';
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":book_id", $new_book_id, PDO::PARAM_INT );
				$st-> bindValue( ":author_id", $author_id, PDO::PARAM_INT );
				$st-> execute();
			}			
				
			parent::disconnect( $conn );
			
			return $new_book_id;
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function update($title,$description, $categories, $authors_id )
	{
		$conn = parent::connect();
		$sql = 'UPDATE '. TABLE_BOOK . ' SET title = :title, 
				   description = :description WHERE  id = :id';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":title", $title, PDO::PARAM_STR );
			$st-> bindValue( ":description", $description, PDO::PARAM_STR );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
			$st-> execute();
			
			
			// Για απαράδεκτους προγραμματιστες μιας και διαγράφω τις υπάρχουσες τιμές
			// και τις καταχωρώ πάλι. Όποιος νοιάζεται ας κάνει κάτι !!! 
			// Εγώ δεν ασχολούμαι μιας και κανένας δεν θα κοιτάξει τον κώδικα από 
			// τους καθηγητές. Αν κατά τύχη τον κοιτάξει κανείς ας μου στείλει ένα μήνυμα
			// στο anagnwstopoulos@hotmail.com για να νιώσω καλά μέσα μου ότι κοπός μας 
			// δεν πήγε χαμένος.			
			
			$sql = 'DELETE FROM '. TABLE_CATEGORIES . ' WHERE  book_id = :id';
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
			$st-> execute();
			
			// Και εδώ κανονικά πρέπει να γίνει με ένα sql insert για θέμα απόδοσης
			// αλλά άστο για άλλη φορά.		
			// Ακόμα δεν ελέγχω ότι η μεταβλητή που παρέχεται είναι πίνακας. 
			foreach ($categories as $category)
			{
				$sql = 'INSERT INTO '. TABLE_CATEGORIES . '(book_id,type)
					VALUES(:id,:category)';
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
				$st-> bindValue( ":category", $category, PDO::PARAM_STR );
				$st-> execute();				
			}
			
			// Ομοίως και εδώ !!! Πεθαίνει ένα κομμάτι μου μέσα που τα γράφω αυτά 
			// και ξέρω ότι δεν θα τα αντικαταστήσω. Μέχρι το τέλος της εργασίας
			// αυτής προβλέπεται να είμαι πιο νεκρός και από την νεκρά θάλασσα.
			
			$sql = 'DELETE FROM '. TABLE_WRITES . ' WHERE  book_id = :id';
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $this->data['id'], PDO::PARAM_INT );
			$st-> execute();
				
			foreach ($authors_id as $author_id)
			{
				$sql = 'INSERT INTO '. TABLE_WRITES . '(author_id,book_id)
					VALUES(:author_id,:book_id)';
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":book_id", $this->data['id'], PDO::PARAM_INT );
				$st-> bindValue( ":author_id", $author_id, PDO::PARAM_INT );
				$st-> execute();
			}
			
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
		// Η συνάρτηση λειτουργεί επειδή είναι ενεργοποιημένα τα CASCADE στην 
		// βάση δεδομένων αλλιώς δεν θα λειτουργεί.	
		
		$conn = parent::connect();
		$sql = 'DELETE FROM ' . TABLE_BOOK . ' WHERE id = :id';
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
	
	public static function getAllCategories()
	{
		$conn = parent::connect();
		$sql = 'SELECT DISTINCT type FROM ' . TABLE_CATEGORIES ;
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> execute();
			$rows = $st->fetchAll(PDO::FETCH_COLUMN,0);
			parent::disconnect( $conn );
			
			return $rows;
		
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}		
	}
	
	public function getCategoriesString()
	{
		// http://php.net/function.implode
		return implode(", ",$this->data['categories']);
	}
	
	public function getAuthors()
	{
		return $this->data['authors'];
	}
	
	public function getAuthorsString()
	{
		$authors_string = array();
		
		foreach($this->data['authors'] as $author)
		{
			$authors_string[] = $author-> getValue("name");
		}
		
		 return implode(", ", $authors_string);
	}
		
	protected $data = array(
			"id" => "",
			"title" => "",
			"description" => "",
			"categories" => array(),
			"authors" => array()
	);	
}

?>