<?php

require_once "common.inc.php";
require_once "../php/User.class.php";

session_start();

displayPageHeader("Σύνδεση χρήστη");
?>

<h2> Παρακαλώ εισάγετε το Username και το κωδικό σας </h2>

<?php

$error = $user = $pass = "";
	
if (isset($_POST['user']))
{
	$user = $_POST['user'];
    $pass = $_POST['pass'];
    $location = $_POST['location'];
    
    //Έλεγχος αν έχουν συμπληρωθεί όλα τα πεδία
    if ($user === "" || $pass === "")
        $error = "<span class='error'>Πρέπει να συμπληρώσετε όλα τα πεδία </span><br><br>";	
    else
    {
    	if (!$result = User::authenticate($user, $pass))
    	{
    		$error = "<span class='error'>Λάθος Username ή Password</span><br><br>";
    	}
    	else
    	{
    		$_SESSION['current_user'] = $result;
  			
    		// Εδώ ελέγχουμε αν η μεταβλητή είναι κενή. Αν είναι κενή τότε 
    		// ανακατευθύνουμε τον χρήστη στην κεντρική σελίδα.
    		if( !(""=== $location) )
    		{
    			// Redirection to your previous page
    			header("Location:" . $location );
    			exit();
    		}
    		else 
    		{
    			// Redirection to main page
    			header("Location:index.php");
    			exit();
    		}
    	}
    }
}
?>

<form method='post' action='login.php'>
	<?= $error ?>
    Username: <input type='text' maxlength='16' name='user' value="<?= $user ?>" ><br>
    Password: <input type='password' maxlength='16' name='pass' value="<?= $pass ?>" ><br>
    <input type='submit' value='Σύνδεση'>
    <input type="reset" value="Επαναφορά">
    <input type="hidden" name="location" value="<?=  (isset($_GET["location"]) ? $_GET["location"]:"") ?>" >
    <!-- Περνάμε ως μεταβλητή στο POST και την μεταβλητή GET["location"] για να είναι προσβάσιμη
         στην επαναφόρτωση της σελίδας. Αυτό εδώ αποκαλύπτει περιττή πληροφορία και καλό ειναι 
         να αλλάξει. Άμα κάποιος έχει όρεξη να το κάνει. TODO: Δηλαδή να περνάμε ταυτόχρονα
         και post και get στην φόρμα. -->
</form>	
<br>
	
<p>Δεν έχεις λογαριασμο;<a href='signup.php'>Δημιουργία τώρα ...</a></p>


<?php 
displayPageFooter()
?>