<?php

require_once "common.inc.php";
require_once "../php/Book.class.php";
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
			
		if($book_editions = Edition::getByBook($book))
		{		
			echo "<h2> Εκδόσεις </h2>";
			
			echo "<table>";
			echo "<tr><td> ISBN </td><td> Εκδότης </td><td> Έκδ. </td><td> Ημ/νια </td><td> Γλώσσα </td> </tr>";
			foreach ($book_editions as $edition)
			{
				echo "<tr><td>";
				echo $edition->getValueEncoded("isbn") ;
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
		}
	}
	else 
		displayPageHeader( "Το βιβλίο δεν βρέθηκε" );
}
else if (isset( $_POST["delete_id"]) && checkAdminLogin())
{
	$book_id = (int) $_POST["delete_id"];
	
	if($book = Book::get($book_id))
	{
		displayPageHeader( "Επιτυχής διαγραφή του βιβλίου: " . 
		         $book->getValueEncoded( "title" ));
		echo $book_id;
	}
	else 
	{
		displayPageHeader( "Αποτυχία διαγραφής" );
	}
	
}
elseif ( isset($_POST["update_id"]) && checkAdminLogin() &&
		 isset($_POST["title"]) && isset($_POST["description"]) &&
		 isset($_POST["categories"]) )
{
	//Εδώ να μπει η συνέχεια...
	$book_id = (int) $_POST["update_id"];
	if($book = Book::get($book_id))
	{
		$title       =  $_POST["title"];
		$description =  $_POST["description"];
		
		// http://stackoverflow.com/questions/10939840/javascript-hidden-input-array
		// Ο πιο αδύναμος, πολύπλοκος, χάλια και ότι αλλο σκεφτείς βάλε κώδικας
		// που έχω γράψει. Όποιον τον νοιάζει ας τον βελτιώσει.
		$categories    =  json_decode($_POST["categories"][0]);
		
		$book->update($title, $description, $categories);//, '', '');
	}
	
	// Redirection to book page
	header("Location:book.php?id=".$book_id);
	exit();

}
elseif ( isset( $_POST["update_id"]) && checkAdminLogin() )
{
	$book_id = (int) $_POST["update_id"];
	
	if($book = Book::get($book_id))
	{
		displayPageHeader( "Ενημέρωση του βιβλίου: " . $book->getValueEncoded( "title" ) );
		?>
		
		<!-- http://jsfiddle.net/fak7p9ky/1/ -->
		<!-- Είναι μακρά από τους χειρότερους και πιο πολύπλοκους κώδικες που έχω φτιάξει !!!-->
    	<!-- Το slice στην τελευταία συνάρτησηχρησιμεύει για την αφαίρεση από την κάθε εγγραφής του 
    	     Διαγραφή. Είναι μακρά από τις χειρότερες πατέντες που έχω κάνει !!!--> 
		
		<script type="text/javascript">
			$(document).ready(function()
			{
    			$("#add_li").click(function ()
    			{
    				$("ol").append("<li>" + $("input").val() + "<a href=\"#\" class=\"remove\">--Διαγραφή--</a></li>");
    			});
   
    			$("ol").on('click','.remove',function()
    			{
    				$(this).parents('li').remove();
    			});

    			$( "#update_form" ).submit(function( event ) 
    	    	{
    				var optionTexts = [];
    				$("ol li").each(function( index ) 
    	    			{ 
    	    				optionTexts.push($(this).text().slice(0,-12)); 
    	    				console.log( index + ": " + $( this ).text() );
    	    			});
        	    	document.getElementById('categories_hidden_field').value = JSON.stringify(optionTexts);
    			});
			});
		</script>
				
		<form id="update_form" method='post' action='book.php'>
		
			<dl>
		    <dt><label for="title">Τίτλος</label></dt> 
		    <dd><textarea rows='3' cols='50' name="title"><?= $book-> getValueEncoded( "title" ) ?></textarea> </dd>
		    
		   	<dt><label for="description">Περιγραφή</label></dt> 
		    <dd><textarea rows='10' cols='50' name="description"><?=  $book-> getValueEncoded( "description" ) ?></textarea></dd>
		   
		   	<dt><label for="categories">Κατηγορία</label></dt> 
		   	
		   	<dd><ol id="categories_ol_id">
		   	<?php
		   	foreach ($book->getValue("categories") as $category)
		   	{
		   		?>
		   		<li><?=$category;?><a href="#" class="remove">--Διαγραφή--</a></li>		   		
		   		<?php 
		   		// http://stackoverflow.com/questions/3287336/best-way-to-submit-ul-via-post
		   	}
		   	?>
			</ol>
		   	
		   	<!-- TODO  Μπορεί να μπει dropdown list -->
			<input type="text" id="element" value="">
			<input type="button" id="add_li" value="Προσθήκη" />
			</dd>
		   	
		   	</dl>
		   	
		   	<input type='hidden' name='categories[]' id="categories_hidden_field" value=" " >
		   	<input type='hidden' name='update_id' value="<?= $book-> getValueEncoded('id' )?>" >
		    <input type='submit' value='Ενημέρωση'>
		    <input type="button" name="Ακύρωση" value="Ακύρωση"
				onclick="window.location='book.php?id=<?= $book_id ?>'" />
				
			
		</form>
		
		<?php 
		
	}
	else 
	{
		displayPageHeader( "Αδυναμία ενημέρωσης εγγραφής" );
	}
	
}

?>

<?php 
displayPageFooter()
?>

