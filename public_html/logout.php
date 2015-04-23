<?php

require_once "common.inc.php";
require_once "../php/User.class.php";

destroySession();

if(checkNotLogin())
{
	displayPageHeader("Δεν είστε συνδεδεμένος");
	?>
	<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
	<?php 
	displayPageFooter();
}
else
{
	displayPageHeader("Αποσυνδεθήκατε με επιτυχία");
	?>
	<a href="index.php" >--Ανακατεύθυνση στην κεντρική σελίδα--</a>
	<?php 
	displayPageFooter();
}
?>