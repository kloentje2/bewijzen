<?php
Class page extends core{
	
	public function show($slug = "") {
		
		$first_time = microtime();
		
		if ($slug == "" OR $slug == NULL) {
			$slug = "index"; 
		}
		$query = $this->con->query("SELECT * FROM pages WHERE slug='".$this->con->real_escape_string($slug)."' LIMIT 1");
		
		if ($query->num_rows == 1) {
		
			$fetch = $query->fetch_assoc();
			
			$data = array(
			"name" => $fetch['name'],
			"content_nl" => $fetch['content_nl'],                                                                   
			"content_en" => $fetch['content_en'],
			"meta_keywords" => $fetch['meta_keywords'],
			"meta_desc" => $fetch['meta_desc']
			);                                                                    
			$data_string = json_encode($data);                                                                                   
																																 
			$ch = curl_init($_SERVER['SERVER_NAME'].'/page?lang='.$_COOKIE['lang']);                                                                      
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");                                                                     
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);  
			curl_setopt($ch, CURLOPT_HEADER, 0);		
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$cookie = $_COOKIE['lang'];
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(   			
				"Content-Type: application/json",                                                                                
				"Content-Length: " . strlen($data_string))                                                                       
			);                                                                                                                   
																																 
			$result = curl_exec($ch);
			
			$end_time = microtime();
			
			$total_time = $end_time - $first_time;
			
			$utime = $this->con->query("
			INSERT INTO
			visit 
			(
			slug,
			ip,
			loadtime,
			day,
			month,
			year
			)
			VALUES
			(
			'".$this->con->real_escape_string($slug)."',
			'".$this->con->real_escape_string($_SERVER['REMOTE_ADDR'])."',
			'".$this->con->real_escape_string($total_time)."',
			'".$this->con->real_escape_string(date("d"))."',
			'".$this->con->real_escape_string(date("m"))."',
			'".$this->con->real_escape_string(date("Y"))."'
			)");
			
			return $result;
			
		
		} else {
			return false;
		}
		
	}
	
	public function getMenu() {
		$query = $this->con->query("SELECT id,name,slug FROM pages WHERE menu = 1 ORDER BY pageorder");
			while ($fetch = $query->fetch_assoc()) {
				if ($string != "") {
					$string .= "__";
					$string .= $fetch['id'];
					$string .= "_";
					$string .= $fetch['slug'];
					$string .= "_";
					$string .= $fetch['name'];
				} else {
					$string .= $fetch['id'];
					$string .= "_";
					$string .= $fetch['slug'];
					$string .= "_";
					$string .= $fetch['name'];
				}
			}
		//var_dump($string);
		$first = explode("__",$string);
		//var_dump($first);
		foreach($first as $key => $val) {
			$second[] = explode("_",$val);
		}
		//var_dump($second);
		/*
			array(2) {
			  [0]=>
			  array(3) {
				[0]=>
				string(1) "1"
				[1]=>
				string(5) "index"
				[2]=>
				string(10) "Voorpagina"
			  }
			  [1]=>
			  array(3) {
				[0]=>
				string(1) "2"
				[1]=>
				string(4) "twee"
				[2]=>
				string(13) "Tweede pagina"
			  }
			}
		*/
		
		return $second;
	}
	
	public function deletePage($pid) {
		$this->con->query("DELETE FROM pages WHERE id = '".$this->con->real_escape_string($pid)."'");
	}
}
?>