<?php

require_once "common.inc.php";
require_once "../php/Publisher.class.php";
require_once "../php/Edition.class.php";

$publisher="";

if ( isset( $_GET["id"] ) )
{
	$publisher_id = (int) $_GET["id"];
	
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
		if(checkAdminLogin())
		{
		?>
							
			<table>
			<tr>
			<td>
				<form method='post' action='publisher.php'>
				<input type='hidden' name='update_id' value="<?= $publisher-> getValueEncoded( "id" )?>" >
				<input type='submit' value='Ενημέρωση εγγραφής'>
				</form>
			</td>
			<td>
				<form method='post' onsubmit= "return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή; Πατώντας ναι θα διαγράφτούν και τα βιβλία τα οποία έχει εκδόσει.')" 
				      action='publisher.php'>
					<input type='hidden' name='delete_id' value="<?= $publisher-> getValueEncoded( "id" )?>" >
					<input type='submit' value='Διαγραφή εγγραφής'>
				</form>
			</td>
			</tr>
			</table>
						
		<?php 
		}
		?>
		
		<!-- https://datatables.net/examples/basic_init/alt_pagination.html -->
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="http://cdn.datatables.net/1.10.6/js/jquery.dataTables.min.js"></script>
		<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.6/css/jquery.dataTables.css"/>
		<script type="text/javaScript">
			$(document).ready(function() 
			{
    			$('#pagination').dataTable( 
    	    	{
        			"pagingType": "full_numbers"
    			});
			});		
		</script>
		
		<h2> Βιβλία που έχει εκδόσει </h2>
	
		<table id='pagination'>
		<thead>
		<tr><td> Βιβλίο </td><td> ISBN </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>
		</thead>
		<tfoot>
		<tr><td> Βιβλίο </td><td> ISBN </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>
		</tfoot>
		<tbody>
				
		<?php 		
		if($books_publisher = Edition::getByPublisher($publisher))
		{			
			foreach ($books_publisher as $edition)
			{
				?>
				<tr><td>			
				<a href="book.php?id=<?=$edition->getBookId();?>"><?=$edition->getBookTitleString();?></a>
				</td><td>
				<a href="edition.php?isbn=<?=$edition->getValueEncoded("isbn");?>"><?=$edition->getValueEncoded("isbn");?></a>
				</td><td>
				<?=$edition->getValueEncoded("edition")?>				
				</td><td>
				<?=$edition->getValueEncoded("date")?>
				</td><td>
				<?=$edition->getValueEncoded("language")?>
				</td> </tr>
				<?php 
			}
		}
		?>
		
		</tbody>
		</table>
		
		<?php 
		if(checkAdminLogin())
		{
			?>
			<table>
				<tr>
				<td>
					<form method='post' action='edition.php?new'>
					<input type='hidden' name='publisher_id' value="<?= $publisher-> getValueEncoded("id")?>" >
					<input type='submit' value='Προσθήκη εγγραφής'>
					</form>
				</td>
				</tr>
			</table>
			<?php 
		} 
	}
	else 
	{
		displayPageHeader("Ο εκδότης δεν βρέθηκε");
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
				
		<?php
		displayPageFooter();
		exit();
	}
}
elseif(isset( $_POST["delete_id"]) && checkAdminLogin())
{
	$publisher_id = (string) $_POST["delete_id"];
	
	if($publisher = Publisher::get($publisher_id))
	{
		$publisher->delete();
		
		displayPageHeader( "Επιτυχής διαγραφή του εκδότη: " .
		$publisher->getValueEncoded( "name" ) ) ;
	
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
					
		<?php 
	}
	else 
	{
		displayPageHeader( "Αποτυχία διαγραφής βιβλίου" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
	
		<?php
		displayPageFooter();
		exit();
	}
}
elseif ( isset($_POST["update_id"]) && checkAdminLogin() &&
		 isset($_POST["name"]) )
{
	$publisher_id = (int) $_POST["update_id"];
	if ($publisher = Publisher::get($publisher_id))
	{
		$name     = $_POST["name"];
		
		$publisher->update($name);
	}
	
	// Redirection to book page
	header("Location:publisher.php?id=".$publisher_id);
	exit();
}
elseif ( isset($_POST["new"]) && checkAdminLogin() &&
		 isset($_POST["name"]) )
{
	$name =  $_POST["name"];
	
	$new_publisher = Publisher::add($name);
	
	// Redirection to book page
	if(!is_null($new_publisher))
	{
		header("Location:publisher.php?id=".$new_publisher);
		exit();
	}
	else
	{
		displayPageHeader( "Αδυναμία δημιουργίας εγγραφής" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
						
		<?php 
		displayPageFooter();
		exit();
	}
}
elseif ( ( isset( $_POST["update_id"]) && checkAdminLogin() ) ||
		 ( isset( $_GET["new"]) && checkAdminLogin() ) )
{
	if( isset( $_POST["update_id"]) && checkAdminLogin() )
	{
		$publisher_id = (int) $_POST["update_id"];
		if ($publisher = Publisher::get($publisher_id))
		{
			displayPageHeader( "Ενημέρωση τoυ εκδότη: " . $publisher-> getValueEncoded( "name" ) );
		}
		else
		{
			displayPageHeader( "Αδυναμία ενημέρωσης εγγραφής" );
			?>
			<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
			<a href="index.php">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
				
			<?php 
			displayPageFooter();
			exit();
		}
	}
	elseif ( isset( $_GET["new"]) && checkAdminLogin() )
	{
		unset($publisher) ;
		displayPageHeader( "Δημιουργία νέου έκδοτη" );	
	}
	?>
	
	<form id="update_form" method='post' action='publisher.php'>
	<dl>
	<dt><label for="name">Όνομα</label></dt> 
	<dd>
		<input type="text" name= "name" id="new_name" value="<?php  if(isset($publisher)) echo $publisher-> getValueEncoded( "name" );?>"> 
	</dd>
	</dl>
	<?php
	if (isset($publisher))
	{
		?>
		<input type='hidden' name='update_id' value="<?=$publisher-> getValueEncoded('id' )?>" >
		<input type='submit' value='Ενημέρωση'>
		<input type="button" name="Ακύρωση" value="Ακύρωση"
		onclick="window.location='publisher.php?id=<?= $publisher-> getValueEncoded( "id" ) ?>'" />
	   	<?php
	}
	else
	{
		?>
		<input type='hidden' name='new' value='-1' >
		<input type='submit' value='Δημιουργία'>
		<input type="button" name="Ακύρωση" value="Ακύρωση"
				onclick="window.location='publisher.php'" />
		<?php
	}  		
	?>
	
	</form>
	<?php 	 	
}
	
?>

<?php 
displayPageFooter()
?>