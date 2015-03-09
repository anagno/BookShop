<?php

require_once "common.inc.php";
require_once "../php/Publisher.class.php";
require_once "../php/Edition.class.php";

$publisher="";

if ( isset( $_GET["id"] ) )
	$publisher_id = (int) $_GET["id"];
else
	$publisher_id = (int) -1;
    // Επειδή η βάση δεδομένων δέχεται μόνο θετικούς ακέραιους αριθμούς

if($publisher = Publisher::get($publisher_id))
{
	displayPageHeader( $publisher->getValueEncoded( "name" ) );
	?>
	
	<!-- http://www.w3schools.com/tags/tag_dl.asp -->

	<dl>

	<dt> Κωδικός </dt> <dd> <?php echo $publisher-> getValueEncoded( "id" ) ?> </dd>
	<dt> Όνομα </dt> <dd> <?php echo $publisher-> getValueEncoded( "name" ) ?> </dd>

	</dl>
	
	<?php 
	
	if($books_publisher = Edition::getByPublisher($publisher))
	{
		echo "<h2> Βιβλία που έχει εκδόσει </h2>";

		echo "<table>";
		echo "<tr><td> Βιβλίο </td><td> ISBN </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>";
		foreach ($books_publisher as $edition)
		{
			echo "<tr><td>";
			echo $edition->getBookTitleString() ;
			echo "</td><td>";			
			echo $edition->getValueEncoded("isbn") ;
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
{
	displayPageHeader("Ο εκδότης δεν βρέθηκε");
}
?>

<?php 
displayPageFooter()
?>