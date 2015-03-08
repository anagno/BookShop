<?php 

/**
 * Common functions needed by all portions of the site
 * Should be included by most of the pages
 */

require_once "../php/User.class.php";

?>

<?php 
/**
 * The function is for displaying the header of the page
 * 
 * @param unknown $page_title
 */
function displayPageHeader($page_title)
{?>

<!DOCTYPE html>
<html>
	<head>
		<!-- Εδώ μπαίνει η επικεφαλίδα -->
		
		<!-- Εδώ μπορεί να γίνεται έλεγχος του μεγέθους της οθόνης και 
		     και να φορτώνονεται αντίστοιχα τα σωστά css. -->
		<link rel="stylesheet" type="text/css" href="css/reset.css"/>
		<link rel="stylesheet" type="text/css" href="css/style.css"/>
		<link rel="stylesheet" type="text/css" href="css/class.css"/>
	
		<title><?php echo $page_title?></title>
		
	
	</head>
	<body>
		<h1><?php echo $page_title?></h1>

<?php 
}

/**
 * The function is for displaying the footer of the page
 *
 * @param unknown $page_title
 */
function displayPageFooter()
{?>

	<footer>
		Copyright &copy; tme119.anagno.me - 2015
	</footer>

	</body>
</html>
	
<?php 
}?>


<?php
/**
 * A function to check that a user is logged in.
 */ 
function checkLogin()
{
	// http://php.net/manual/en/function.session-start.php
	session_start();
	
	// Check that a there is not a user logged in
	if( !isset($_SESSION["current_user"]) or !is_a($_SESSION["current_user"],User) ) 
	{
		unset($_SESSION["current_user"]);
		
		// http://stackoverflow.com/questions/14523468/redirecting-to-previous-page-after-login-php
		// Όποιος νοιάζετε ας βάλει και τα σωστά if για να μην γίνονται malicious redirects
		header("Location: login.php?location=" . urlencode($_SERVER['REQUEST_URI']));
		return false;
	}
	
	return true;	
}

function checkNotLogin()
{
	// http://php.net/manual/en/function.session-start.php
	session_start();

	// Check that a there is not a user logged in
	if( !isset($_SESSION["current_user"]) or !is_a($_SESSION["current_user"],User) )
	{
		return true;
	}

	return false;
}

/**
 * A function to disconnect the user that is logged in.
 */
function destroySession()
{
	$_SESSION=array();
	
	if (session_id() != "" || isset($_COOKIE[session_name()]))
		setcookie(session_name(), '', time()-2592000, '/');
	
	session_destroy();	
}

?>
		