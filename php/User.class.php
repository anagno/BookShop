<?php

require_once "DataObject.class.php";

/**
 * Defining the name of the table that is used to store the users
 */
define('TABLE_USER', 'users');

class User extends DataObject 
{
	public static function authenticate($username, $password)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_USER . ' WHERE username = :username 
				                and password = :password';
		
		$password = self::passwordHash($password);
				
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":username", $username, PDO::PARAM_STR );
			$st-> bindValue( ":password", $password, PDO::PARAM_STR );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $row ) 
				return new User( $row );

		}
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function get($id) 
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_USER . ' WHERE uid = :id';
		try 
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":id", $id, PDO::PARAM_INT );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $row ) 
				return new User( $row );
		} 
		catch ( PDOException $e ) 
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function check($username)
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_USER . ' WHERE username = :username';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":username", $username, PDO::PARAM_INT );
			$st-> execute();
			$row = $st-> fetch();
			parent::disconnect( $conn );
			
			if ( $row )
				return TRUE;
			else
				return FALSE;
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}		
	}
	
	public static function register($username,$password,$first_name,$last_name,
			                  $gender,$email)
	{
		//Check if the username is used and return if it.
		if(self::check($username))
			return FALSE;
				
		$password = self::passwordHash($password);
		
		$conn = parent::connect();
		$sql = 'INSERT INTO ' . TABLE_USER . ' (username,password,first_name,last_name,
			                  join_date,gender,email) VALUES(:username,:password,
				              :first_name,:last_name,NOW(),:gender,:email)';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":username", $username, PDO::PARAM_STR );
			$st-> bindValue( ":password", $password, PDO::PARAM_STR );
			$st-> bindValue( ":first_name", $first_name, PDO::PARAM_STR );
			$st-> bindValue( ":last_name", $last_name, PDO::PARAM_STR );
			$st-> bindValue( ":gender", $gender, PDO::PARAM_BOOL );
			$st-> bindValue( ":email", $email, PDO::PARAM_STR );
			$st-> execute();
			parent::disconnect( $conn );
				
			return TRUE;
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public static function delete($uid)
	{
		$conn = parent::connect();
		$sql = 'DELETE FROM ' . TABLE_USER . ' WHERE uid = :uid';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":uid", $uid, PDO::PARAM_INT );
			$st-> execute();
			parent::disconnect( $conn );
	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function getGenderString()
	{
		// Γαμώ τις αρχιδιές της php που αντί να μου το τραβά σαν bool
		// θεωρεί ότι το ένα είναι string. Όποιος φτιάχνει untyped γλώσσες
		// πρέπει να πεθάνει !!!
		
		if ($this->data['gender'] ==="1")
		{
			return "Ανδρας";
		}
		else
		{
			return "Γυναίκα";
		}
	}
	
	/**
	 * Για να μην αποθηκεύονται απ` ευθείας οι κωδικοί
	 * Κανονίκα πρέπει να γίνει και salting εκός από hashing σε θεωρητικό επιπέδο
	 * αλλά δεν νομίζω να ενδιαφερθεί και κανείς οπότε δεν γαμιέται
	 * Όποιος έχει όρεξη ας το κάνει αλλιώς πολύ εινια και το md5 hash
	 */
	private static function passwordHash($password)
	{
		return md5($password);
	}
	
	protected $data = array(
			"uid" => "",
			"username" => "",
			"password" => "",
			"first_name" => "",
			"last_name" => "",
			"join_date" => "",
			"gender" => "",
			"email" => ""
	);
	
}

?>
