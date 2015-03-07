<?php

require_once "../php/Book.class.php";

if ( isset( $_GET["book_id"] ) )
	$book_id = (int) $_GET["book_id"];

if(!$book = Book::get($book_id))
{
	die ("Error: Book not found");
}

?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> Book id </dt> <dd> <?php echo $book-> getValueEncoded( "id" ) ?> </dd>
<dt> Title </dt> <dd> <?php echo $book-> getValueEncoded( "title" ) ?> </dd>
<dt> Description </dt> <dd> <?php echo $book-> getValueEncoded( "description" ) ?> </dd>
<dt> Categories </dt> <dd> <?php echo $book->getCategoriesString() ?> </dd>
<dt> Authors </dt> <dd> <?php echo $book->getAuthorsString() ?> </dd>

</dl>