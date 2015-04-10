<?php

require_once "common.inc.php";
require_once "../php/Author.class.php";
require_once "../php/Book.class.php";

$author = "";

if ( isset( $_GET["id"] ) )
{
	$author_id = (int) $_GET["id"];
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
		if(checkAdminLogin())
		{
		?>
							
			<table>
			<tr>
			<td>
				<form method='post' action='author.php'>
				<input type='hidden' name='update_id' value="<?= $author-> getValueEncoded( "id" )?>" >
				<input type='submit' value='Ενημέρωση εγγραφής'>
				</form>
			</td>
			<td>
				<form method='post' onsubmit= "return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή;')" 
				      action='author.php'>
					<input type='hidden' name='delete_id' value="<?= $author-> getValueEncoded( "id" )?>" >
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
		
		<h2> Βιβλία που έχει γράψει ο συγγραφέας </h2>
		
		<table id='pagination'>
		<thead>
		<tr><td> Κωδικός </td><td> Τίτλος </td> </tr>
		</thead>
		<tfoot>
		<tr><td> Κωδικός </td><td> Τίτλος </td> </tr>
		</tfoot>
		<tbody>
		
		<?php 
		$books_author= Book::getByAuthor($author);
		if($books_author)
		{
			foreach ($books_author as $book)
			{
				?>
				<tr><td>
				<?=$book->getValueEncoded("id") ;?>
				</td><td>
				<a href="book.php?id=<?=$book->getValueEncoded('id');?>"><?=$book->getValueEncoded('title');?></a>
				</td></tr>
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
					<form method='post' action='book.php?new'>
					<input type='hidden' name='author_id' value="<?= $author-> getValueEncoded("id")?>" >
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
		displayPageHeader("Ο συγγραφέας δεν βρέθηκε");
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
	$author_id = (string) $_POST["delete_id"];

	if($author = Author::get($author_id))
	{
		$author->delete();

		displayPageHeader( "Επιτυχής διαγραφή του συγγραφέα: " .
				$author->getValueEncoded( "name" ) ) ;

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
	$author_id = (int) $_POST["update_id"];
	if ($author = Author::get($author_id))
	{
		$name     = $_POST["name"];

		$author->update($name);
	}

	// Redirection to book page
	header("Location:author.php?id=".$author_id);
	exit();
}
elseif ( isset($_POST["new"]) && checkAdminLogin() &&
		isset($_POST["name"]) )
{
	$name =  $_POST["name"];

	$new_author = Author::add($name);

	// Redirection to book page
	if(!is_null($new_author))
	{
		header("Location:author.php?id=".$new_author);
		exit();
	}
	else
	{
		displayPageHeader( "Αδυναμία δημιουργίας συγγραφέα" );
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
		$author_id = (int) $_POST["update_id"];
		if ($author = Author::get($author_id))
		{
			displayPageHeader( "Ενημέρωση τoυ συγγραφέα: " . $author-> getValueEncoded( "name" ) );
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
		unset($author) ;
		displayPageHeader( "Δημιουργία νέου συγγραφέα" );	
	}
	?>
	
	<form id="update_form" method='post' action='author.php'>
	<dl>
	<dt><label for="name">Όνομα</label></dt> 
	<dd>
		<input type="text" name= "name" id="new_name" value="<?php  if(isset($author)) echo $author-> getValueEncoded( "name" );?>"> 
	</dd>
	</dl>
	
	<?php
	if (isset($author))
	{
		?>
		<input type='hidden' name='update_id' value="<?=$author-> getValueEncoded('id' )?>" >
		<input type='submit' value='Ενημέρωση'>
		<input type="button" name="Ακύρωση" value="Ακύρωση"
		onclick="window.location='author.php?id=<?= $author-> getValueEncoded( "id" ) ?>'" />
	   	<?php
	}
	else
	{
		?>
		<input type='hidden' name='new' value='-1' >
		<input type='submit' value='Δημιουργία'>
		<input type="button" name="Ακύρωση" value="Ακύρωση"
				onclick="window.location='author.php'" />
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