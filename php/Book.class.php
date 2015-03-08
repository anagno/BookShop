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
	
	public function getCategoriesString()
	{
		// http://php.net/function.implode
		return implode(", ",$this->data['categories']);
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