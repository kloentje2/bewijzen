<?php
Class user extends core {
	
	private function password($string) {
		return password_hash($string,PASSWORD_BCRYPT);
	}
	
	public function create($username,$password,$mail) {
		if (!isset($username) OR $username == "") {
			return false;
		}
		
		if (!isset($password) OR $password == "") {
			return false;
		}
		
		if (!isset($mail) OR $mail == "") {
			return false;
		}
		
		$query = $this->con->query("
		INSERT INTO
		users
		(
		username,
		password,
		mail,
		regip
		)
		VALUES
		(
		'".$this->con->real_escape_string($username)."',
		'".$this->con->real_escape_string($this->password($password))."',
		'".$this->con->real_escape_string($mail)."',
		'".$this->con->real_escape_string($_SERVER['REMOTE_ADDR'])."'
		)
		");
		
		if ($query) {
			return true;
		} else {
			return false;
		}
	}
	
	public function login($username,$password) {
		if (!isset($username) OR $username == "") {
			return false;
		}
		
		if (!isset($password) OR $password == "") {
			return false;
		}
		
		$query = $this->con->query("SELECT password FROM users WHERE username = '".$this->con->real_escape_string($username)."' LIMIT 1");
		if ($query->num_rows == 1) {
			$fetch = $query->fetch_assoc();
			if (password_verify($password,$fetch['password'])) {
				//All good, great
				$this->con->query("UPDATE users SET lastip='".$this->con->real_escape_string($_SERVER['REMOTE_ADDR'])."' WHERE username='".$this->con->real_escape_string($username)."'");
				return true;
			} else {
				//YOU DONT BELONG HERE
				return false;
			}
		} else {
			//I DONT KNOW YOU
			return false;
		}
	}
	
	public function sendNewPassword($to,$password) {
		$subject = "Jouw nieuwe wachtwoord";

		$message = "
		<html>
		<head>
		<title>Jouw nieuwe wachtwoord</title>
		</head>
		<body>
		Je hebt een nieuw wachtwoord gekregen op de website van ".parent::getSetting("company_name")."<br>
		Je nieuwe wachtwoord is: ".$password."
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <no-reply@smt-systems.nl>' . "\r\n";

		mail($to,$subject,$message,$headers);
	}
	
	public function changePassword($uid,$password) {
		$update = $this->con->query("UPDATE users SET password = '".$this->con->real_escape_string($this->password($password))."' WHERE id = '".$this->con->real_escape_string($uid)."'");
		if ($update) {
			return true;
		} else {
			return false;
		}
	}
	
	public function deleteUser($uid) {
		$this->con->query("DELETE FROM users WHERE id = '".$this->con->real_escape_string($uid)."'");
	}
	
}
?>