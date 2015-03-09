<?php

require_once "common.inc.php";
require_once "../php/Edition.class.php";
require_once "../php/Book.class.php";



if ( isset( $_GET["isbn"] ) )
	$edition_isbn = (string) $_GET["isbn"];
else 
	$edition_isbn = (string) "0000000000000";
	// Ένας άκυρος isbn που δεν θα φέρει αποτελέσματα από την βάση 

if($edition = Edition::get($edition_isbn))
{
	displayPageHeader( $edition->getBookTitleString() );
	?>
	
	<!-- http://www.w3schools.com/tags/tag_dl.asp -->

	<dl>

	<dt> ISBN </dt> <dd> <?php echo $edition-> getValueEncoded( "isbn" ) ?> </dd>
	<dt> Τίτλος </dt> <dd> <?php echo $edition-> getBookTitleString() ?> </dd>
	<dt> Εκδότης </dt> <dd> <?php echo $edition->getPublishersString() ?> </dd>
	<dt> Έκδοση </dt> <dd> <?php echo $edition->getValueEncoded( "edition" ) ?> </dd>
	<dt> Ημερομηνία έκδοσης </dt> <dd> <?php echo $edition->getValueEncoded( "date" ) ?> </dd>
	<dt> Γλώσσα </dt> <dd> <?php echo $edition->getValueEncoded( "language" ) ?> </dd>

	</dl>
	
	<?php 
}
else 
	displayPageHeader( "Το βιβλίο δεν βρέθηκε" );
?>	


<?php 
displayPageFooter()
?>
