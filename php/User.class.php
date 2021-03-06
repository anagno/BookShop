﻿<?php

require_once "DataObject.class.php";

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
	
	public static function getAllUsers()
	{
		$conn = parent::connect();
		$sql = 'SELECT * FROM ' . TABLE_USER ;
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> execute();
			$rows = $st-> fetchAll(PDO::FETCH_COLUMN);
			parent::disconnect( $conn );
				
			if ( $rows )
			{
				// Θέλει μία βελτιστοποίηση... Όποιος έχει όρεξη ας την κάνει ...
				$users = array();
				
				foreach ($rows as $row)
				{
					array_push($users, self::get($row) );
				}
				
				return $users;
			}
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
		$sql = 'INSERT INTO ' . TABLE_USER . ' (username,password,is_admin,first_name,
				          last_name,join_date,gender,email) VALUES(:username,
				          :password, 0,:first_name,:last_name,NOW(),:gender,:email)';
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
	
	public function updatePrivileges()
	{
		//Check if the username exists.
		if(self::check($this->data['username']))
		{		
			$conn = parent::connect();
			$sql = 'UPDATE '. TABLE_USER . ' SET is_admin = 1 
									WHERE username = :username';
			try
			{
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":username", $this->data['username'], PDO::PARAM_STR );
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
		else 
			return FALSE;
	}
	
	public function removePrivileges()
	{
		//Check if the username exists.
		if(self::check($this->data['username']))
		{
			$conn = parent::connect();
			$sql = 'UPDATE '. TABLE_USER . ' SET is_admin = 0
									WHERE username = :username';
			try
			{
				$st = $conn-> prepare( $sql );
				$st-> bindValue( ":username", $this->data['username'], PDO::PARAM_STR );
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
		else
			return FALSE;
	}
	
	public function delete()
	{
		$conn = parent::connect();
		$sql = 'DELETE FROM ' . TABLE_USER . ' WHERE uid = :uid';
		try
		{
			$st = $conn-> prepare( $sql );
			$st-> bindValue( ":uid", $this->data['uid'], PDO::PARAM_INT );
			$st-> execute();
			parent::disconnect( $conn );
	
		}
		catch ( PDOException $e )
		{
			parent::disconnect( $conn );
			die( "Query failed: " . $e->getMessage() );
		}
	}
	
	public function isAdmin()
	{
		if ($this->data['is_admin'] ==="1")
		{
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}
	
	public function getUserName()
	{
		return $this->data["first_name"] . " " .
				$this->data["last_name"];
	}
	
	public function getGenderString()
	{
		
		
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
	 * Κανονίκα πρέπει να γίνει και salting εκτός από hashing σε θεωρητικό επιπέδο
	 */
	private static function passwordHash($password)
	{
		return md5($password);
	}
	
	protected $data = array(
			"uid" => "",
			"username" => "",
			"password" => "",
			"is_admin" => "",
			"first_name" => "",
			"last_name" => "",
			"join_date" => "",
			"gender" => "",
			"email" => ""
	);
	
}

?>
