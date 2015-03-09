<?php

require_once "common.inc.php";
require_once "../php/Book.class.php";
require_once "../php/Edition.class.php";

$book = "";

if ( isset( $_GET["book_id"] ) )
	$book_id = (int) $_GET["book_id"];
else 
	$book_id = (int) -1;
	// Επειδή η βάση δεδομένων δέχεται μόνο θετικούς ακέρους 

if($book = Book::get($book_id))
{
	displayPageHeader( $book->getValueEncoded( "title" ) );
	?>
	
	<!-- http://www.w3schools.com/tags/tag_dl.asp -->

	<dl>

	<dt> Αύξων Αριθμός </dt> <dd> <?php echo $book-> getValueEncoded( "id" ) ?> </dd>
	<dt> Τίτλος </dt> <dd> <?php echo $book-> getValueEncoded( "title" ) ?> </dd>
	<dt> Περιγραφή </dt> <dd> <?php echo $book-> getValueEncoded( "description" ) ?> </dd>
	<dt> Κατηγορία </dt> <dd> <?php echo $book->getCategoriesString() ?> </dd>
	<dt> Συγγραφείς </dt> <dd> <?php echo $book->getAuthorsString() ?> </dd>

	</dl>
	
	<?php 
		
	if($book_editions = Edition::getByBook($book))
	{		
		echo "<h2> Εκδόσεις </h2>";
		
		echo "<table>";
		echo "<tr><td> ISBN </td><td> Εκδότης </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>";
		foreach ($book_editions as $edition)
		{
			echo "<tr><td>";
			echo $edition->getValueEncoded("isbn") ;
			echo "</td><td>";
			echo $edition->getPublishersString();
			echo "</td><td>";
			echo $edition->getValueEncoded("edition");
			echo "</td><td>";
			echo $edition->getValueEncoded("date");
			echo "</td><td>";
			echo $edition->getValueEncoded("language");
			echo "</td> </tr>";
		}
		
		echo "</table>";
	}
}
else 
	displayPageHeader( "Το βιβλίο δεν βρέθηκε" );

?>

<?php 
displayPageFooter()
?>

