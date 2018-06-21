<?php
Class core {
	
	protected $con;
	
	public function __construct($con) {
		
		$this->con = $con;
		
	}
	
	public function getSetting($setting) {
		$query = $this->con->query("SELECT value FROM settings WHERE setting = '".$this->con->real_escape_string($setting)."' LIMIT 1");
		$fetch = $query->fetch_assoc();
		return htmlspecialchars($fetch['value']);
	}
	
}
?>