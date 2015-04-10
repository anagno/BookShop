<?php

require_once "common.inc.php";
require_once "../php/Book.class.php";
require_once "../php/Author.class.php";
require_once "../php/Edition.class.php";

$book = "";

if ( isset( $_GET["id"] ) )
{
	$book_id = (int) $_GET["id"];
	
	if($book = Book::get($book_id))
	{
		displayPageHeader( $book->getValueEncoded( "title" ) );
		?>
		
		<!-- http://www.w3schools.com/tags/tag_dl.asp -->
	
		<dl>
	
		<dt> Αύξων Αριθμός </dt> <dd> <?php echo $book-> getValueEncoded( "id" ) ?> </dd>
		<dt> Τίτλος </dt> <dd> <?php echo $book-> getValueEncoded( "title" ) ?> </dd>
		<dt> Περιγραφή </dt> <dd> <?php echo $book-> getValueEncoded( "description" ) ?> </dd>
		<dt> Κατηγορία </dt> <dd> <?php echo $book->getCategoriesString() ?> </dd>
		<dt> Συγγραφείς </dt> <dd> <?php echo $book->getAuthorsString() ?> </dd>
	
		</dl>
		
		<?php 		
		if(checkAdminLogin())
		{
			?>
				
			<table>
			<tr>
			<td>
				<form method='post' action='book.php'>
				<input type='hidden' name='update_id' value="<?= $book-> getValueEncoded('id' )?>" >
				<input type='submit' value='Ενημέρωση εγγραφής'>
				</form>
			</td>
			<td>
				<form method='post' onsubmit= "return confirm('Είστε σίγουρος ότι θέλετε να διαγράψετε την εγγραφή;')" 
				      action='book.php'>
					<input type='hidden' name='delete_id' value="<?= $book-> getValueEncoded('id' )?>" >
					<input type='submit' value='Διαγραφή εγγραφής'>
				</form>
			</td>
			</tr>
			</table>
			
			<?php 
		}
		
		if($book_editions = Edition::getByBook($book))
		{		
			echo "<h2> Εκδόσεις </h2>";
			
			echo "<table>";
			echo "<tr><td> ISBN </td><td> Εκδότης </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>";
			foreach ($book_editions as $edition)
			{
				echo "<tr><td>";
				?>
				<a href="edition.php?isbn=<?=$edition->getValueEncoded("isbn");?>" class="remove"><?=$edition->getValueEncoded("isbn");?></a>
				<?php 
				echo "</td><td>";
				echo $edition->getPublishersString();
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
		?>

		<?php
		if(checkAdminLogin())
		{
			?>
			<table>
			<tr>
			<td>
				<form method='post' action='edition.php?new'>
				<input type='hidden' name='book_id' value="<?= $book-> getValueEncoded('id' )?>" >
				<input type='submit' value='Προσθήκη εγγραφής'>
				</form>
			</td>
			
			</tr>
			</table>
			<?php 
		}
	}
	else 
		displayPageHeader( "Το βιβλίο δεν βρέθηκε" );
}
elseif (isset( $_POST["delete_id"]) && checkAdminLogin())
{
	$book_id = (int) $_POST["delete_id"];
	
	if($book = Book::get($book_id))
	{
		$book->delete();
		displayPageHeader( "Επιτυχής διαγραφή του βιβλίου '" . 
		         $book->getValueEncoded( "title" ) . "' με Α/Α " . $book_id ) ;
		
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" class="remove">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
					
		<?php 
	}
	else 
	{
		displayPageHeader( "Αποτυχία διαγραφής βιβλίου" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" class="remove">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
			
		<?php
		displayPageFooter();
		exit();
	}
	
}
elseif ( isset($_POST["update_id"]) && checkAdminLogin() &&
		 isset($_POST["title"]) && isset($_POST["description"]) &&
		 isset($_POST["categories"]) && isset($_POST["authors_id"]))
{
	// Εδώ να μπει η συνέχεια...
	$book_id = (int) $_POST["update_id"];
	if($book = Book::get($book_id))
	{
		$title       = $_POST["title"];
		$description = $_POST["description"];
		
		// http://stackoverflow.com/questions/10939840/javascript-hidden-input-array
		// Ο πιο αδύναμος, πολύπλοκος, χάλια και ότι αλλο σκεφτείς βάλε κώδικας
		// που έχω γράψει. Όποιον τον νοιάζει ας τον βελτιώσει.
		$categories  = json_decode($_POST["categories"]);
		// http://usrportage.de/archives/808-Convert-an-array-of-strings-into-an-array-of-integers.html
		$authors_id  = array_map(create_function('$value', 'return (int)$value;'),
				$_POST["authors_id"]);

		$book->update($title, $description, $categories, $authors_id);
	}
	
	// Redirection to book page
	header("Location:book.php?id=".$book_id);
	exit();

}
elseif ( isset($_POST["new"]) && checkAdminLogin() &&
		 isset($_POST["title"]) && isset($_POST["description"]) &&
		 isset($_POST["categories"]) && isset($_POST["authors_id"]))
{
	$title       = $_POST["title"];
	$description = $_POST["description"];
	
	// http://stackoverflow.com/questions/10939840/javascript-hidden-input-array
	// Ο πιο αδύναμος, πολύπλοκος, χάλια και ότι αλλο σκεφτείς βάλε κώδικας
	// που έχω γράψει. Όποιον τον νοιάζει ας τον βελτιώσει.
	$categories  = json_decode($_POST["categories"]);
	// http://usrportage.de/archives/808-Convert-an-array-of-strings-into-an-array-of-integers.html
	$authors_id  = array_map(create_function('$value', 'return (int)$value;'),
	$_POST["authors_id"]);
	
	$book_id = Book::add($title, $description, $categories, $authors_id);
	
	if(!is_null($book_id))
	{
	// Redirection to book page
	header("Location:book.php?id=".$book_id);
	exit();
	}
	else 
	{
		displayPageHeader( "Αποτυχία δημιουργίας βιβλίου" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php" class="remove">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
					
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
		$book_id = (int) $_POST["update_id"];
		if ($book = Book::get($book_id))
		{
			displayPageHeader( "Ενημέρωση του βιβλίου: " . $book->getValueEncoded( "title" ) );
		}
		else
		{
			displayPageHeader( "Αδυναμία ενημέρωσης εγγραφής" );
			?>
			<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
			<a href="index.php" class="remove">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
			
			<?php 
			displayPageFooter();
			exit();
		}		
	}
	elseif ( isset( $_GET["new"]) && checkAdminLogin() )
	{
		unset($book) ;
		displayPageHeader( "Δημιουργία νέου βιβλίου" );
	}
	?>
		
	<!-- http://jsfiddle.net/fak7p9ky/1/ -->
	<!-- Είναι μακρά από τους χειρότερους και πιο πολύπλοκους κώδικες που έχω φτιάξει !!!-->
    <!-- Το slice στην τελευταία συνάρτησηχρησιμεύει για την αφαίρεση από την κάθε εγγραφής του 
         Διαγραφή. Είναι μακρά από τις χειρότερες πατέντες που έχω κάνει !!!--> 
		
	<script type="text/javascript">
		$(document).ready(function()
		{
    		$("#add_li_category").click(function ()
    		{
       			if($("#new_category").val())
       			{
       				$("ol").append("<li>" + $("#new_category").val() + "<a href=\"#\" class=\"remove\">--Διαγραφή--</a></li>");
       			}
    		});
   
   			$("ol").on('click','.remove',function()
   			{
   				$(this).parents('li').remove();
   			});

   			$("#add_li_authors").click(function ()
   	    	{
   				//console.log( $("#new_author option:selected").text()+ " " +$("#new_author").val()  );
   	    		$("ul").append("<li>" + $("#new_author option:selected").text() + "<input type='hidden' name='authors_id[]' id='authors_hidden_filed' value=" + $("#new_author").val() + "><a href=\"#\" class=\"remove\">--Διαγραφή--</a></li>");
   	    	});

   	    	$("ul").on('click','.remove',function()
   	    	{
   	    		$(this).parents('li').remove();
   	    	});

   			$( "#update_form" ).submit(function( event ) 
   	    	{
   	   	    	// TODO  Να μπει έλεγχος ότι υπάρχει τουλάχιστον ένας συγγραφές 
   	   	    	//       και μία κατηγορία.
   				var categories = [];
   				$("ol li").each(function( index ) 
   	    		{ 
   					categories.push($(this).text().slice(0,-12)); 
   	    			//console.log( index + ": " + $( this ).text() );
   	    		});
       	    	document.getElementById('categories_hidden_field').value = JSON.stringify(categories);
   			});
		});
	</script>
				
	<form id="update_form" method='post' action='book.php'>
		
		<dl>
	    <dt><label for="title">Τίτλος</label></dt> 
	    <dd><textarea rows='3' cols='50' name="title"><?php  if(isset($book)) echo $book-> getValueEncoded( "title" ) ?></textarea> </dd>
		    
	   	<dt><label for="description">Περιγραφή</label></dt> 
	    <dd><textarea rows='10' cols='50' name="description"><?php  if(isset($book)) echo  $book-> getValueEncoded( "description" ) ?></textarea></dd>
		   
	   	<dt><label for="categories">Κατηγορία</label></dt> 
		   	
	   	<!-- Δεν μπορεί να αλλάζει το ol διότι χρησιμοποιείται στην javascript
	   	     ως κριτήριο διαχωρισμού -->
	   	<dd><ol id="categories_ol_id">
	   	<?php
	   	if(isset($book))
	   	{
	   		foreach ($book->getValue("categories") as $category)
	   		{
		   		?>
		   		<li><?=$category;?><a href="#" class="remove">--Διαγραφή--</a></li>		   		
		   		<?php 
	   			// http://stackoverflow.com/questions/3287336/best-way-to-submit-ul-via-post
	   		}
	   	}
	   	?>
		</ol>
	   	
	   	<!-- Αν η βάση αποκτήσει ποτέ πολλές εγγραφές αυτή η μέθοδος σίγουρα δεν είναι 
	   	     αποτελεσματική αλλά βαριέμαι να την βελτιστοποιήσω. -->
		<datalist id="category_list">
			<?php
	   		foreach (Book::getAllCategories() as $category_new)
	   		{
	   			?>
	   			<option value='<?=$category_new;?>'>		   		
	   			<?php 
	   			// http://stackoverflow.com/questions/3287336/best-way-to-submit-ul-via-post
	   		}
	   		?>
 		</datalist>
 		<input type="text" list="category_list" id="new_category" value="">
		<input type="button" id="add_li_category" value="Προσθήκη" />
		</dd>
		
		<dt><label for="authors">Συγγραφείς</label></dt> 
	   	
	   	<!-- Δεν μπορεί να αλλάζει το ul διότι χρησιμοποιείται στην javascript
	   	     ως κριτήριο διαχωρισμού -->
	   	<dd><ul id="authors_ul_id">
	   	<?php
	   	if(isset($book))
	   	{
	   		foreach ($book->getAuthors() as $author)
	   		{
		   		?>
		   		<li><?=$author->getValueEncoded('name');?><input type='hidden' name='authors_id[]' id='authors_hidden_filed' value='<?=$author->getValueEncoded('id')?>'><a href="#" class="remove">--Διαγραφή--</a></li>		   		
		   		<?php 
	   			// http://stackoverflow.com/questions/3287336/best-way-to-submit-ul-via-post
	   		}
	   	}
	   	?>
		</ul>
	   	
	   	<!-- TODO  Μπορεί να μπει dropdown list -->
		<!-- <input type="text" id="" value=""> -->
		<select id="new_author">
		<?php 
		foreach (Author::getAllAuthros() as $author)
	   	{
	   		?>
	   		<option value="<?=$author->getValueEncoded('id') ?>"> <?=$author->getValueEncoded('name')?></option>   		
	   		<?php 
	   	}
	   	?>		
		</select>
		<input type="button" id="add_li_authors" value="Προσθήκη" />
		
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="#" class="remove">--Προσθήκη συγγραφέα--</a>
		</dd>
				   	
	   	</dl>
		   	
	   	<input type='hidden' name='categories' id="categories_hidden_field" value=" " >
		   	
	   	<?php 
	   	if (isset($book))
	   	{
	   		?>
	   		<input type='hidden' name='update_id' value="<?=$book-> getValueEncoded('id' )?>" >
	   		<input type='submit' value='Ενημέρωση'>
	   		<input type="button" name="Ακύρωση" value="Ακύρωση"
			onclick="window.location='book.php?id=<?= $book_id ?>'" />
   		<?php
	   	} 
	   	else 
	   	{
	   		?>
	   		<input type='hidden' name='new' value='-1' >
	   		<input type='submit' value='Δημιουργία'>
	   		<input type="button" name="Ακύρωση" value="Ακύρωση"
			onclick="window.location='book.php'" />
   		<?php
	   	} 
	   	?>    
							
	</form>
				
	<?php 
}
else 
{
	//TODO
}
?>


<?php 
displayPageFooter();
?>

