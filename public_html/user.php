<?php

require_once "common.inc.php";
require_once "../php/User.class.php";

if(checkLogin())
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
	<?php
	if(checkAdminLogin())
	{
		?>		
		<table>
			<tr>
			<td>
				<form method='post' action='user.php'>
				<input type='hidden' name='update_id' value="<?= $user-> getValueEncoded( "username" )?>" >
				<input type='submit' value='Ενημέρωση στοιχείων'>
				</form>
			</td>
		</table>					
		<?php 
	}
	
	if($user->isAdmin())
	{
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
			<?php 
			if(!$user_list->isAdmin())
			{
				?>
				<a href="User.php?id=<?=$user_list->getValueEncoded("username");?>">Αναβάθμιση δικαιωμάτων</a>
				<?php
			}
			else 
			{
				?>
				<a href="User.php?id=<?=$user_list->getValueEncoded("username");?>">Υποβάθμιση δικαιωμάτων</a>
				<?php								
			}			
			?>
			</td><td>
			<a href="User.php?id=<?=$user_list->getValueEncoded("username");?>">Διαγραφή χρήστη</a>
			</td> </tr>
			<?php 
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




