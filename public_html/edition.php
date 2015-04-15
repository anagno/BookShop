<?php

require_once "common.inc.php";
require_once "../php/Edition.class.php";
require_once "../php/Book.class.php";
require_once "../php/Publisher.class.php";
require_once "../php/Paperbook.class.php";



if ( isset($_GET["isbn"]) )
{
	$edition_isbn = (string) $_GET["isbn"];
	
	if($edition = Edition::get($edition_isbn))
	{
		displayPageHeader( $edition->getBookTitleString() );
		?>
		
		<!-- http://www.w3schools.com/tags/tag_dl.asp -->
	
		<dl>
	
		<dt> ISBN </dt> <dd> <?php echo $edition-> getValueEncoded( "isbn" ) ?> </dd>					
		<dt> Τίτλος </dt> <dd> <a href="book.php?id=<?=$edition->getBookId();?>"><?=$edition-> getBookTitleString();?></a></dd>
		<dt> Εκδότης </dt> <dd> <a href="publisher.php?id=<?=$edition->getPublishersId();?>"><?=$edition->getPublishersString();?></a></dd>
		<dt> Έκδοση </dt> <dd> <?php echo $edition->getValueEncoded( "edition" ) ?> </dd>
		<dt> Ημερομηνία έκδοσης </dt> <dd> <?php echo $edition->getValueEncoded( "date" ) ?> </dd>
		<dt> Γλώσσα </dt> <dd> <?php echo $edition->getValueEncoded( "language" ) ?> </dd>
	
		</dl>
		
		<?php
		if(checkAdminLogin())
		{
		?>
						
			<table>
			<tr>
			<td>
				<form method='post' action='edition.php'>
				<input type='hidden' name='update_id' value="<?= $edition-> getValueEncoded( "isbn" )?>" >
				<input type='submit' value='Ενημέρωση εγγραφής'>
				</form>
			</td>
			<td>
				<form method='post' onsubmit= "return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή;')" 
				      action='edition.php'>
					<input type='hidden' name='delete_id' value="<?= $edition-> getValueEncoded( "isbn" )?>" >
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
				
		<h2> Αντίτυπα </h2>
		
		<table id='pagination'>
		<thead>
		<tr><td> Κωδικός </td><td> Βιβλιοδεσία </td><td> Διαθέσιμο </td></tr>
		</thead>
		<tfoot>
		<tr><td> Κωδικός </td><td> Βιβλιοδεσία </td><td> Διαθέσιμο </td></tr>
		</tfoot>
		<tbody>
		
		<?php 
		if($paperbook_editions = Paperbook::getByEdition($edition))
		{		
			foreach ($paperbook_editions as $paperbook)
			{
				?>
				<tr><td>
				<a href="paperbook.php?id=<?=$paperbook->getValueEncoded("id");?>"><?=$paperbook->getValueEncoded("id");?></a>
				</td><td>
				<?=$paperbook->getValueEncoded("binding");?>
				</td><td>
				<?php ?>
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
				<form method='post' action='paperbook.php?new'>
				<input type='hidden' name='edition_isbn' value="<?= $edition-> getValueEncoded( "isbn" )?>" >
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
		displayPageHeader( "Η έκδοση δεν βρέθηκε" );
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
	$edition_id = (string) $_POST["delete_id"];
	
	if($edition = Edition::get($edition_id))
	{
		$edition->delete();
		
		displayPageHeader( "Επιτυχής διαγραφή της έκδοσης με isbn: " .
		$edition->getValueEncoded( "isbn" ) ) ;
	
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="book.php?id=<?=$edition->getBookId();?>" >--Ανακατεύθυνση στην στο βιβλίο--</a>
					
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
elseif ( isset($_POST["update_id"]) && checkAdminLogin() &&
		 isset($_POST["book_id"]) && isset($_POST["pusblisher_id"]) && 
		 isset($_POST["edition"]) && isset($_POST["date"]) && 
		 isset($_POST["language"])  )
{
	$edition_id = (int) $_POST["update_id"];
	if ($edition = Edition::get($edition_id))
	{
		$book_id       = $_POST["book_id"];
		$pusblisher_id = $_POST["pusblisher_id"];
		$edition_num   = $_POST["edition"];
		$date          = $_POST["date"];
		$language      = $_POST["language"];

		$edition->update($book_id, $pusblisher_id, $edition_num, $date, $language);
	}

	// Redirection to book page
	header("Location:edition.php?isbn=".$edition_id);
	exit();

}
elseif ( isset($_POST["new"]) && checkAdminLogin() &&
		 isset($_POST["book_id"]) && isset($_POST["pusblisher_id"]) && 
		 isset($_POST["edition"]) && isset($_POST["date"]) && 
		 isset($_POST["language"]) && isset($_POST["isbn"]))
{
	
	$isbn          =  $_POST["isbn"];
	$book_id       = $_POST["book_id"];
	$pusblisher_id = $_POST["pusblisher_id"];
	$edition_num   = $_POST["edition"];
	$date          = $_POST["date"];
	$language      = $_POST["language"];

	$status = Edition::add($isbn, $book_id, $pusblisher_id, $edition_num, $date, $language);
	

	// Redirection to book page
	if($status)
	{
		header("Location:edition.php?isbn=".$isbn);
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
		$edition_id = (int) $_POST["update_id"];
		if ($edition = Edition::get($edition_id))
		{
			displayPageHeader( "Ενημέρωση της έκδοσης: " . $edition-> getValueEncoded( "isbn" ) );
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
		unset($edition) ;
		displayPageHeader( "Δημιουργία νέας έκδοσης" );	
	}
	?>
	
	<form id="update_form" method='post' action='edition.php'>
	
	<dl>
		<?php 
	    if(!isset($edition))
	    {
	        ?>
			<dt><label for="isbn">ISBN</label></dt> 
	    	<dd> 
	    		<input type="text" name= "isbn" id="new_isbn" value="">
	    	</dd>
	    	<?php 
	    }
	    ?>	
	    <dt><label for="title">Τίτλος</label></dt> 
	    <dd>
	    <select id="new_title" name="book_id">
	    <?php 
	    if(isset($edition))
	    {
			foreach (Book::getAllTitles() as $title)
	   		{
	   			if ($title->getValueEncoded('id') === $edition->getBookId())
	   			{
	   				?>
		   			<option selected="selected" value="<?=$title->getValueEncoded('id') ?>"><?=$title->getValueEncoded('title')?></option>
	   				<?php 
		   		}
	   			else 
	   			{
	   			?>
	   				<option value="<?=$title->getValueEncoded('id') ?>"> <?=$title->getValueEncoded('title')?></option>   		
	   			<?php
	   			}
	   		}  
	    }
	    else 
	    {
	   		if(isset($_POST["book_id"]))
	   		{
	   			$book_id = $_POST["book_id"];
				foreach (Book::getAllTitles() as $title)
	   			{
	   				if ($title->getValueEncoded('id') === $book_id)
	   				{
	   					?>
		   		  		<option selected="selected" value="<?=$title->getValueEncoded('id') ?>"><?=$title->getValueEncoded('title')?></option>
	   					<?php 
		   			}
	   				else 
	   				{
	   				?>
	   					<option value="<?=$title->getValueEncoded('id') ?>"> <?=$title->getValueEncoded('title')?></option>   		
	   				<?php
	   				}
	   			}
	   		}  
	   		else
	   		{
	   			foreach (Book::getAllTitles() as $title)
	   			{
	   				?>
	   				<option value="<?=$title->getValueEncoded('id') ?>"> <?=$title->getValueEncoded('title')?></option>
	   				<?php
	   			}
	   		}
	    }
	    
	   	?>	
	   	</select>
	    </dd>
	    
	    <dt><label for="pusblisher_id">Εκδότης</label></dt> 
	    <dd>
	    <select id="new_publisher" name="pusblisher_id">
	   	<?php 
	   	if(isset($edition))
	   	{
			foreach (Publisher::getAllPublishers() as $publisher)
	   		{
	   			if ($publisher->getValueEncoded('id') === $edition->getPublishersId())
	   			{
	   			?>
		   			<option selected="selected" value="<?=$publisher->getValueEncoded('id') ?>"> <?=$publisher->getValueEncoded('name')?></option>
	   			<?php	 
	   			}
	   			else 
	   			{
	   			?>
	   			<option value="<?=$publisher->getValueEncoded('id') ?>"> <?=$publisher->getValueEncoded('name')?></option>   		
	   			<?php
	   			} 
	   		}
	   	}
	   	elseif(isset($_POST["publisher_id"])) 
	   	{
	   		$publisher_id = $_POST["publisher_id"];
	   		
	   		foreach (Publisher::getAllPublishers() as $publisher)
	   		{
	   			if ($publisher->getValueEncoded('id') === $publisher_id)
	   			{
	   				?>
	   				<option selected="selected" value="<?=$publisher->getValueEncoded('id') ?>"> <?=$publisher->getValueEncoded('name')?></option>
	   			   	<?php	 
	   			}
	   			else 
	   			{
	   			   	?>
	   			 	<option value="<?=$publisher->getValueEncoded('id') ?>"> <?=$publisher->getValueEncoded('name')?></option>   		
	   			 	<?php
	   			} 
	   		}
	   	}
	   	else
	   	{
	   		foreach (Publisher::getAllPublishers() as $publisher)
	   		{
	   			?>
	   			<option value="<?=$publisher->getValueEncoded('id') ?>"> <?=$publisher->getValueEncoded('name')?></option>   		
	   			<?php
	   		} 
	   	}
	   	?>	
	   	</select>
	    </dd>
	    
	    <dt><label for="edition">Έκδοση</label></dt> 
	    <dd>
	    	<input type="text" name= "edition" id="new_edition" value="<?php  if(isset($edition)) echo $edition-> getValueEncoded( "edition" );?>">
	    </dd>
	    
	    <dt><label for="date">Ημερομηνία έκδοσης</label></dt> 
	    <dd>
	    	<input type="text" name= "date" id="new_date" value="<?php  if(isset($edition)) echo $edition-> getValueEncoded( "date" );?>">
	    </dd>  
	    
	    <dt><label for="language">Γλώσσα</label></dt> 
	    <dd>
	    	<input type="text" name= "language" id="new_language" value="<?php  if(isset($edition)) echo $edition-> getValueEncoded( "language" );?>">
	    </dd>     
	    
	    
	    </dl>
	    
	    <?php 
	   	if (isset($edition))
	   	{
	   		?>
	   		<input type='hidden' name='update_id' value="<?=$edition-> getValueEncoded('isbn' )?>" >
	   		<input type='submit' value='Ενημέρωση'>
	   		<input type="button" name="Ακύρωση" value="Ακύρωση"
			onclick="window.location='edition.php?isbn=<?= $edition-> getValueEncoded( "isbn" ) ?>'" />
   		<?php
	   	} 
	   	else 
	   	{
	   		?>
	   		<input type='hidden' name='new' value='-1' >
	   		<input type='submit' value='Δημιουργία'>
	   		<?php
	   		if(isset($_POST["book_id"]))
	   		{
	   			$book_id = $_POST["book_id"];
	   			?>
	   			<input type="button" name="Ακύρωση" value="Ακύρωση"
				onclick="window.location='book.php?id=<?= $book_id?>'" />
	   			<?php
	   		}
	   		elseif(isset($_POST["publisher_id"]))
	   		{
	   			$publisher_id = $_POST["publisher_id"];
	   			?>
	   			<input type="button" name="Ακύρωση" value="Ακύρωση"
	   			onclick="window.location='publisher.php?id=<?= $publisher_id?>'" />
	   			<?php
	   		}
	   		else
	   		{
	   			?>
		   		<input type="button" name="Ακύρωση" value="Ακύρωση"
				onclick="window.location='edition.php'" />
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
