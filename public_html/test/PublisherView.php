<?php

require_once "../php/Publisher.class.php";

if ( isset( $_GET["publisher_id"] ) )
	$publisher_id = (int) $_GET["publisher_id"];

if(!$publisher = Publisher::get($publisher_id))
{
	die("Error: Author not found");
}

?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> Publisher id </dt> <dd> <?php echo $publisher-> getValueEncoded( "id" ) ?> </dd>
<dt> Name </dt> <dd> <?php echo $publisher-> getValueEncoded( "name" ) ?> </dd>

</dl>