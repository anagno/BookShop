<?php

require_once "common.inc.php";
require_once "../php/User.class.php";
require_once "../php/Paperbook.class.php";

if(isset( $_POST["delete_id"]) && checkAdminLogin())
{
	$user_id = (int) $_POST["delete_id"];
	
	if($user = User::get($user_id))
	{
		// Εδώ ανοίγω την πόρτα των καημών διότι δεν κάνω έλεγχο αν υπάρχει έστω και ένας 
		// διαχειριστής στην εφαρμογή με αποτέλεσμα κάποιος έξυπνος να κλειδώσει εντελώς 
		// την εφαρμογή. Αλλά δεν γαμιέται ...
		$user->delete($user_id);
	
		displayPageHeader( "Επιτυχής διαγραφή του χρήστη " );
	
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="user.php">--Επιστροφή--</a>
				
		<?php 
	}
	else 
	{
		displayPageHeader( "Αποτυχία διαγραφής χρήστη" );
		?>
		<!-- TODO Να φτιάξουμε το λινκ να πηγαίνει κάπου χρήσιμα -->
		<a href="index.php">--Ανακατεύθυνση στην κεντρική σελίδα--</a>
		
		<?php
		displayPageFooter();
		exit();
	}
}
elseif ( isset($_POST["update_id"]) && checkAdminLogin() )
{
	$user_id = (int) $_POST["update_id"];
	
	// Και εδώ τα τραβάει ο κώλος μου διότι δεν κάνω ποτέ έλεγχο αν υπάρχει τουλάχιστον ένας
	// διαχειριστής, με αποτέλεσμα να κλειδωθούν όλοι εκτός της εφαρμογής.
	if($user = User::get($user_id))
	{
		if(!$user->isAdmin())
		{
			$user->updatePrivileges();			
		}
		else 
		{
			$user->removePrivileges();
		}
	}

	// Redirection to user page
	header("Location:user.php");
	exit();	
}
elseif(checkLogin())
{
	$user = $_SESSION['current_user'];
	
	if($user->isAdmin())
	{
		displayPageHeader("Διαχειριστής: " . $user->getUserName() );
	}
	else 
	{
		displayPageHeader("Χρήστης: " . $user->getUserName() );		
	}
	?>
	
	<!-- http://www.w3schools.com/tags/tag_dl.asp -->
	
	<dl>
	
	<dt> Username </dt> <dd> <?php echo $user-> getValueEncoded( "username" ) ?> </dd>
	<dt> Όνομα </dt> <dd> <?php echo $user-> getValueEncoded( "first_name" ) ?> </dd>
	<dt> Επίθετο </dt> <dd> <?php echo $user-> getValueEncoded( "last_name" ) ?> </dd>
	<dt> Ημερομηνία εγγραφής </dt> <dd> <?php echo $user-> getValueEncoded( "join_date" ) ?> </dd>
	<dt> Φύλλο </dt> <dd> <?php echo $user->  getGenderString() ?> </dd>
	<dt> Email </dt> <dd> <?php echo $user-> getValueEncoded( "email" ) ?> </dd>
	
	</dl>
	
	
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
	if($user->isAdmin())
	{
		?>		
		<h2> Ενεργοί χρήστες </h2>
	
		<table id='pagination'>
		<thead>
		<tr><td> Username </td><td> Όνομα </td><td> Επίθετο </td><td> Ημερομηνία εγγραφής </td><td> Φύλλο </td><td> Email </td><td> </td><td> </td> </tr> 
		</thead>
		<tfoot>
		<tr><td> Username </td><td> Όνομα </td><td> Επίθετο </td><td> Ημερομηνία εγγραφής </td><td> Φύλλο </td><td> Email </td><td> </td><td> </td> </tr>
		</tfoot>
		<tbody>
		
		<?php 		
		foreach (User::getAllUsers() as $user_list)
		{
			?>
			<tr><td>		
			<?php 
			echo $user_list->getValueEncoded("username");
			if($user_list->getValueEncoded("username") === $user->getValueEncoded("username"))
				echo " (Τρέχον χρήστης)";
			?>	
			</td><td>
			<?=$user_list->getValueEncoded("first_name")?>				
			</td><td>
			<?=$user_list->getValueEncoded("last_name")?>
			</td><td>
			<?=$user_list->getValueEncoded("join_date")?>
			</td><td>
			<?=$user_list->getGenderString()?>
			</td><td>
			<?=$user_list->getValueEncoded("email")?>
			</td><td>
			
			<form method='post' action='user.php'>
			<input type='hidden' name='update_id' value="<?=$user_list->getValueEncoded("uid");?>" >
			<?php 			
			if(!$user_list->isAdmin())
			{
				?>
				<input type='submit' value='Αναβάθμιση'>
				<?php
			}
			else 
			{
				?>
				<input type='submit' value='Υποβάθμιση'>
				<?php								
			}			
			?>
			</form>
			
			</td><td>
			<form method='post' action='user.php'>
			<input type='hidden' name='delete_id' value="<?=$user_list->getValueEncoded("uid");?>" >
			<input type='submit' value='Διαγραφή'>
			</form>
			</td> </tr>
			<?php 
		}
		?>
		
		</tbody>
		</table>
		<?php		
	}
	else 
	{
		?>
		<h2> Τα βιβλία που έχω </h2>
		
		<table id='pagination'>
		<thead>
		<tr><td> Τίτλος </td><td> Ημερομηνία ενοικίασης  </td></tr>
		</thead>
		<tfoot>
		<tr><td> Τίτλος </td><td> Ημερομηνία ενοικίασης  </td></tr>
		</tfoot>
		<tbody>
		<?php
		if($books_rented = Paperbook::getByUser($user))
		{
			foreach ($books_rented as $book_rent)
			{
				?>
					<tr><td>
					<a href="book.php?id=<?=$book_rent->getBookId();?>"><?=$book_rent->getBookTitleString();?></a>
					</td><td>
					<?=$book_rent->lastStartBorrowDate();?>
					</td><td>
					<?php 
				}
			}
			?>
				
			</tbody>
			</table>
	<?php 		
	}
}
?>

<?php
displayPageFooter()
?>




