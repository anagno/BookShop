<?php

require_once "common.inc.php";
require_once "../php/User.class.php";

displayPageHeader("Δημιουργία χρήστη");
?>

<h2> Εισάγετε τα στοιχεία σας </h2>

<?php

$error = $user = $pass = $first = $last = $mail = "";
$gender = (int) 0;

// Πρέπει να μην είμαστε συνδεδεμένοι για να δημιουργήσουμε χρήστη
if( checkNotLogin() )
{
	if (isset($_POST['user']))
	{
		$user   = $_POST['user'];
		$pass   = $_POST['pass'];
		$first  = $_POST['first'];
		$last   = $_POST['last'];
		$gender = $_POST['gender'];
		$mail   = $_POST['mail'];
		
		if($user === "" ||  $pass === "" || $last === "" || $first === "" || $gender === "" || $mail === "")
		{
			$error = "Συμπληρώστε όλα τα πεδία";
		}
		elseif(User::check($user)) 
		{
			$error = "Το username χρησιμοποιείται ήδη";
		}
		else
		{
			if(User::register($user, $pass, $first, $last, $gender, $mail))
			{
				header("Location:login.php");
				exit;
			}
			else 
			{
				die("Κάτι πήγε πολύ στραβά και δεν έπρεπε");
			}
		}
	}
}
else 
{
	$error = "Είστε ήδη συνδεδεμένος. Αποσυνδεθείτε για να δημιουργήσετε ένα καινούργιο χρήστη.";
}

?>


<!-- Η διαδραστικότητα αυτού εδώ μπορεί να φτάσει στον ουρανό. 
     TODO: Να το κάνουμε πιο διαδραστικό  -->
     
<form method='post' action='signup.php'> 
	<span class='error'><?= $error ?></span> <br>
    Username <input type='text' maxlength='16' name='user' value= "<?= $user ?>" > <br>
    Κωδικός <input type='text' maxlength='16' name='pass' value="<?= $pass ?>"><br>
    Όνομα   <input type='text' maxlength='16' name='first' value="<?= $first ?>"><br>
    Επίθετο <input type='text' maxlength='16' name='last' value="<?= $last ?>"><br>
    Φύλο   <input type='radio' name='gender' value="1" checked >:Άνδρας 
           <input type='radio' name='gender' value="0">:Γυναίκα <br>
    Email   <input type='text' maxlength='16' name='mail' value="<?= $mail ?>"><br>
    <input type='submit' value='Δημιουργία'>
    <input type="reset" value="Επαναφορά">
</form>

<?php 
displayPageFooter()
?>