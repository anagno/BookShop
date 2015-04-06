<?php

require_once "common.inc.php";
require_once "../php/Author.class.php";
require_once "../php/Book.class.php";

$author = "";

if ( isset( $_GET["id"] ) )
	$author_id = (int) $_GET["id"];
else 
	$author_id = (int) -1;
	// Επειδή η βάση δεδομένων δέχεται μόνο θετικούς ακέραιους αριθμούς

if($author = Author::get($author_id))
{
	displayPageHeader( $author->getValueEncoded( "name" ) );
	?>
	
	<!-- http://www.w3schools.com/tags/tag_dl.asp -->
	
	<dl>
	
	<dt> Αύξων Αριθμός </dt> <dd> <?php echo $author-> getValueEncoded( "id" ) ?> </dd>
	<dt> Όνομα </dt> <dd> <?php echo $author-> getValueEncoded( "name" ) ?> </dd>
	
	</dl>
	
	<?php 
	
	$books_author= Book::getByAuthor($author);
	if($books_author)
	{
		echo "<h2> Βιβλία που έχει γράψει ο συγγραφέας </h2>";

		echo "<table>";
		echo "<tr><td> Κωδικός </td><td> Τίτλος </td> </tr>";	
		foreach ($books_author as $book)
		{
			echo "<tr><td>";
			echo $book->getValueEncoded("id") ;
			echo "</td><td>";
			echo "<a href='book.php?id="; 
			echo $book->getValueEncoded("id") ;
		 	echo "'>";
			echo $book->getValueEncoded("title");
			echo "</a>";
			echo "</td> </tr>";	
		}
	
		echo "</table>";
	} 
}
else 
{
	displayPageHeader("Ο συγγραφέας δεν βρέθηκε");
}
?>

<?php 
displayPageFooter()
?>