<?php

require_once "common.inc.php";
require_once "../php/Author.class.php";
require_once "../php/Book.class.php";

$author = "";

if ( isset( $_GET["author_id"] ) )
	$author_id = (int) $_GET["author_id"];

if($author = Author::get($author_id))
{
	displayPageHeader( $author->getValueEncoded( "name" ) );
}
else 
{
	displayPageHeader("Ο συγγραφέας δεν βρέθηκε");
}

?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> Αύξων Αριθμός </dt> <dd> <?php echo $author-> getValueEncoded( "id" ) ?> </dd>
<dt> Όνομα </dt> <dd> <?php echo $author-> getValueEncoded( "name" ) ?> </dd>

</dl>


<?php 

$books_author= Book::getByAuthor($author);

echo "<h2> Βιβλία που έχει γράψει ο συγγραφέας </h2>";

if($books_author)
{
	echo "<table>";
	echo "<tr><td> Κωδικός </td><td> Τίτλος </td> </tr>";	
	foreach ($books_author as $book)
	{
		echo "<tr><td>";
		echo $book->getValueEncoded("id") ;
		echo "</td><td>";
		echo $book->getValueEncoded("title");
		echo "</td> </tr>";	
	}
	
	echo "</table>";
}
?>

<?php 
displayPageFooter()
?>