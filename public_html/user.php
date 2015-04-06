<?php

require_once "common.inc.php";
require_once "../php/User.class.php";

if(checkLogin())
{
	$user = $_SESSION['current_user'];
	
	if($user->isAdmin())
	{
		displayPageHeader("Διαχειριστής: " . $user->getValueEncoded( "first_name" ) . " " .
				$user->getValueEncoded( "last_name" ) );
	}
	else 
	{
		displayPageHeader("Χρήστης: " . $user->getValueEncoded( "first_name" ) . " " .
				$user->getValueEncoded( "last_name" ) );		
	}
}
?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> Username </dt> <dd> <?php echo $user-> getValueEncoded( "username" ) ?> </dd>
<dt> First name </dt> <dd> <?php echo $user-> getValueEncoded( "first_name" ) ?> </dd>
<dt> Last name </dt> <dd> <?php echo $user-> getValueEncoded( "last_name" ) ?> </dd>
<dt> Joined on </dt> <dd> <?php echo $user-> getValueEncoded( "join_date" ) ?> </dd>
<dt> Gender </dt> <dd> <?php echo $user->  getGenderString() ?> </dd>
<dt> Email address </dt> <dd> <?php echo $user-> getValueEncoded( "email" ) ?> </dd>

</dl>

<?php 
displayPageFooter()
?>




