<?php

require_once "../php/Author.class.php";

if ( isset( $_GET["author_id"] ) )
	$author_id = (int) $_GET["author_id"];

if(!$author = Author::get($author_id))
{
	die("Error: Author not found");
}

?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> Author id </dt> <dd> <?php echo $author-> getValueEncoded( "id" ) ?> </dd>
<dt> Name </dt> <dd> <?php echo $author-> getValueEncoded( "name" ) ?> </dd>

</dl>