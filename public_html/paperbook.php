<?php
require_once "common.inc.php";
require_once "../php/Paperbook.class.php";
require_once "../php/User.class.php";

$paperbook = "";

if ( isset( $_GET["id"] ) )
{
	$paperbook_id = (int) $_GET["id"];
	
	if($paperbook = Paperbook::get($paperbook_id))
	{
		displayPageHeader( "Αντίτυπο με κωδικό: " . $paperbook->getValueEncoded( "id" ) );
		?>
		
		<dl>
	
		<dt> Αύξων Αριθμός </dt> <dd> <?php echo $paperbook-> getValueEncoded( "id" ) ?> </dd>
		<dt> Τίτλος </dt> <dd> <a href="book.php?id=<?=$paperbook->getBookId();?>"><?=$paperbook-> getValue( "edition" )->getBookTitleString();?></a></dd>
		<dt> ISBN </dt> <dd> <a href="edition.php?isbn=<?=$paperbook->getIsbn();?>"><?=$paperbook->getIsbn();?></a> </dd>
		<dt> Βιβλιοδεσία </dt> <dd> <?php echo $paperbook-> getValueEncoded( "binding" ) ?> </dd>
		<dt> Τοποθεσία </dt> <dd> <?php echo $paperbook->getValueEncoded( "location" ) ?> </dd>			
	
		</dl>
		
		<?php
		if(checkAdminLogin())
		{
		?>			
			<table>
			<tr>
			<td>
				<form method='post' action='paperbook.php'>
				<input type='hidden' name='update_id' value="<?= $paperbook-> getValueEncoded( "id" )?>" >
				<input type='submit' value='Ενημέρωση εγγραφής'>
				</form>
			</td>
			<td>
				<form method='post' onsubmit= "return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή;')" 
				      action='paperbook.php'>
					<input type='hidden' name='delete_id' value="<?= $paperbook-> getValueEncoded( "id" )?>" >
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
		
		<?php
		if(checkAdminLogin())
		{
			?>
		
			<h2> Ιστορικό </h2>
			
			<?php 
			if(!$paperbook->isBorrowed())
			{
				?>
				<form method='post' action='paperbook.php'>
				<input type='hidden' name='rent_id' value="<?= $paperbook-> getValueEncoded( "id" )?>" >
				<select id="new_rent_user" name="user">
				<?php 				
				foreach (User::getAllUsers() as $user)
	    		{
	    			?>
	    			<option value="<?=$user->getValueEncoded('username') ?>"> <?=$user->getUserName()?></option>
	    			<?php
	   			}	
	   			?>
				</select>					
				<input type='submit' value='Δανεισμός'>
				</form>
			<?php 	
			}
			else 
			{
				?>
				<form method='post' action='paperbook.php'>
				<input type='hidden' name='rent_id' value="<?= $paperbook-> getValueEncoded( "id" )?>" >
				<input type='submit' value='Επιστροφή'>
				</form>
				<?php 
			}
			?>
		
			<table id='pagination'>
			<thead>
			<tr><td> Ημερομηνία έναρξης </td><td> Ημερομηνία λήξης </td><td> Χρήστης </td></tr>
			</thead>
			<tfoot>
			<tr><td> Ημερομηνία έναρξης </td><td> Ημερομηνία λήξης </td><td> Χρήστης </td></tr>
			</tfoot>
			<tbody>
			<?php 
			if($history_log = $paperbook->getValue('borrows'))
			{		
				foreach ($history_log as $log)
				{
					?>
					<tr><td>
					<?=$log['start_period']; ?>
					</td><td>
					<?=$log['end_period']; ?>
					</td><td>
					<?=$log['username']; ?>
					</td></tr>		
					<!-- https://www.youtube.com/watch?v=wOwblaKmyVw -->			
					<?php 
				}
			}
			?>
		
			</tbody>
			</table>
			
		<?php 
		}	
	}
	else
	{
		displayPageHeader( "Το αντίτυπο δεν βρέθηκε" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
		
		<?php
		displayPageFooter();
		exit();
	}
}
elseif(isset( $_POST["rent_id"]) && checkAdminLogin())
{
	$rent_id = (string) $_POST["rent_id"];

	if($paperbook = Paperbook::get($rent_id))
	{
		if(!$paperbook->isBorrowed())
		{
			if(isset( $_POST["user"]))
			{
				$user = (string) $_POST["user"];
				$paperbook->bookRent($user);
			
				displayPageHeader( "Επιτυχής ενοικιάση του βιβλίου με κωδικό " .
						$paperbook->getValueEncoded( "id" ) ) ;
			}	
		}
		else 
		{
			$paperbook->bookReturn();
				
			displayPageHeader( "Επιτυχής επιστροφή του βιβλίου με κωδικό " .
					$paperbook->getValueEncoded( "id" ) ) ;			
		}
		
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="paperbook.php?id=<?=$rent_id;?>" >--Ανακατεύθυνση πίσω στο αντίτυπο--</a>				
		<?php 
		
	}
	else 
	{
		displayPageHeader( "Αποτυχία ενημέρωσης εγγραφής" );
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
	$paperbook_id = (string) $_POST["delete_id"];

	if($paperbook = Paperbook::get($paperbook_id))
	{
		$paperbook->delete();

		displayPageHeader( "Επιτυχής διαγραφή του αντιτύπου με κωδικό " .
				$paperbook->getValueEncoded( "id" ) ) ;

		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="edition.php?isbn=<?=$paperbook->getIsbn();?>" >--Ανακατεύθυνση στην έκδοση--</a>
					
		<?php 
	}
	else 
	{
		displayPageHeader( "Αποτυχία διαγραφής βιβλίου" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
	
		<?php
		displayPageFooter();
		exit();
	}
}
elseif ( isset($_POST["new"]) && checkAdminLogin() &&
		 isset($_POST["isbn"]) && isset($_POST["binding"]) &&
		 isset($_POST["location"]) )
{
	$isbn     = $_POST["isbn"];
	$binding  = $_POST["binding"];
	$location = $_POST["location"];

	$paperbook_id = Paperbook::add($isbn, $binding, $location);


	// Redirection to book page
	if(!is_null($paperbook_id))
	{
		header("Location:paperbook.php?id=".$paperbook_id);
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
elseif ( isset($_POST["update_id"]) && checkAdminLogin() &&
		 isset($_POST["isbn"]) && isset($_POST["binding"]) &&
		 isset($_POST["location"]) )
{
	$paperbook_id = (int) $_POST["update_id"];
	
	if ($paperbook = Paperbook::get($paperbook_id))
	{
		$isbn     = $_POST["isbn"];
		$binding  = $_POST["binding"];
		$location = $_POST["location"];

		$paperbook->update($isbn, $binding, $location);
	}

	// Redirection to book page
	header("Location:paperbook.php?id=".$paperbook_id);
	exit();

}
elseif ( ( isset( $_POST["update_id"]) && checkAdminLogin() ) ||
		( isset( $_GET["new"]) && checkAdminLogin() ) )
{
	
	if( isset( $_POST["update_id"]) && checkAdminLogin() )
	{
		$paperbook_id = (int) $_POST["update_id"];
		if ($paperbook = Paperbook::get($paperbook_id))
		{
			displayPageHeader( "Ενημέρωση του αντιτύπου: " . $paperbook-> getValueEncoded( "id" ) );
		}
		else
		{
			displayPageHeader( "Αδυναμία ενημέρωσης εγγραφής" );
			?>
				<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
				<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
				
				<?php 
				displayPageFooter();
				exit();
			}
	}
	elseif ( isset( $_GET["new"]) && checkAdminLogin() )
	{
		unset($paperbook) ;
		displayPageHeader( "Δημιουργία νέου αντιτύπου" );	
	}
	?>
		
	<form id="update_form" method='post' action='paperbook.php'>
		
	<dl>
		
		<dt><label for="isbn">ISBN</label></dt> 
		
		<dd>
	   	<select id="new_isbn" name="isbn">		
		<?php 
	   	if(isset($paperbook))
	   	{
			foreach (Edition::getAllIsbn() as $title)
	  		{
	  			if ($title->getValueEncoded('isbn') === $paperbook->getIsbn())
	  			{
	 				?>
	   				<option selected="selected" value="<?=$title->getValueEncoded('isbn') ?>"><?=$title->getValueEncoded('isbn')?> ( <?= $title->getBookTitleString()?> )</option>
	   				<?php 
		   		}
	   			else 
	   			{
	   			?>
		   			<option value="<?=$title->getValueEncoded('isbn') ?>"> <?=$title->getValueEncoded('isbn')?> ( <?= $title->getBookTitleString()?> ) </option>   		
		   		<?php
	   			}
	   		}  
	    }
	    elseif (isset($_POST["edition_isbn"]))
   		{
   			$edition_isbn = $_POST["edition_isbn"];
   			
   			foreach (Edition::getAllIsbn() as $title)
   			{
   				if ($title->getValueEncoded('isbn') === $edition_isbn)
   				{
   					?>
   				   	<option selected="selected" value="<?=$title->getValueEncoded('isbn') ?>"><?=$title->getValueEncoded('isbn')?> ( <?= $title->getBookTitleString()?> )</option>
   				   	<?php 
   				}
   				else 
   				{
   					?>
   					<option value="<?=$title->getValueEncoded('isbn') ?>"> <?=$title->getValueEncoded('isbn')?> ( <?= $title->getBookTitleString()?> ) </option>   		
   					<?php
   				}
   			} 
   		}
	    else 
	    {
	    	foreach (Edition::getAllIsbn() as $title)
	    	{
	    		?>
	    		<option value="<?=$title->getValueEncoded('isbn') ?>"> <?=$title->getValueEncoded('isbn')?> ( <?= $title->getBookTitleString()?> ) </option>
	    		<?php
	   		}	    		
	    }	
	    	?>
		</select>
		</dd>
		
		<dt><label for="binding">Βιβλιοδεσία</label></dt> 
	   	<dd>
	   	<input type="text" name= "binding" id="new_binding" value="<?php  if(isset($paperbook)) echo $paperbook-> getValueEncoded( "binding" );?>">
		</dd>
	  		
		<dt><label for="location">Τοποθεσία</label></dt> 
	   	<dd>
	   	<input type="text" name= "location" id="new_location" value="<?php  if(isset($paperbook)) echo $paperbook-> getValueEncoded( "location" );?>">
		</dd>
	   					
	</dl>
	   		
	<?php 
	if (isset($paperbook))
   	{
   		?>
   		<input type='hidden' name='update_id' value="<?=$paperbook-> getValueEncoded( "id" )?>" >
   		<input type='submit' value='Ενημέρωση'>
   		<input type="button" name="Ακύρωση" value="Ακύρωση"
		onclick="window.location='paperbook.php?id=<?= $paperbook-> getValueEncoded( "id" ) ?>'" />
  		<?php
   	}
   	else 
   	{
   		?>
   		<input type='hidden' name='new' value='-1' >
   		<input type='submit' value='Δημιουργία'>
   		<?php
   		if(isset($_POST["edition_isbn"]))
   		{
   			$edition_isbn = $_POST["edition_isbn"];
   			?>
   			<input type="button" name="Ακύρωση" value="Ακύρωση"
   			onclick="window.location='edition.php?isbn=<?= $edition_isbn?>'" />
   			<?php
   		}
   		else 
   		{
   			?>
   			<input type="button" name="Ακύρωση" value="Ακύρωση"
   			onclick="window.location='paperbook.php'" />
   			<?php
   		}
	   		
   	}
   	?>
		
	</form>
		
	<?php 
}

?>

<?php 
displayPageFooter();
?>