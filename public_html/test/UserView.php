<?php

require_once "../php/User.class.php";

if ( isset( $_GET["user_id"] ) )
	$user_id = (int) $_GET["user_id"];

if(!$user = User::get($user_id))
{
	die("Error: User not found");
}
?>

<!-- http://www.w3schools.com/tags/tag_dl.asp -->

<dl>

<dt> User id </dt> <dd> <?php echo $user-> getValueEncoded( "uid" ) ?> </dd>
<dt> Username </dt> <dd> <?php echo $user-> getValueEncoded( "username" ) ?> </dd>
<dt> First name </dt> <dd> <?php echo $user-> getValueEncoded( "first_name" ) ?> </dd>
<dt> Last name </dt> <dd> <?php echo $user-> getValueEncoded( "last_name" ) ?> </dd>
<dt> Joined on </dt> <dd> <?php echo $user-> getValueEncoded( "join_date" ) ?> </dd>
<dt> Gender </dt> <dd> <?php echo $user->  getGenderString() ?> </dd>
<dt> Email address </dt> <dd> <?php echo $user-> getValueEncoded( "email" ) ?> </dd>

</dl>




