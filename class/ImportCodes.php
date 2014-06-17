<?php 
class ImportCodes {
	
	public $codes = array();
	public $errors = array();
	
	protected $errFile = 'errors.log';
	protected $host = 'localhost';
	protected $user = 'root';
	protected $pass = '';
	protected $db = 'teste';
	protected $myconn;

	
	public function getConnection() {
		return $this->myconn;
	}
	
	public function __construct()
	{		
		$con = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
		if (!$con) {
			die('Could not connect to database!');
		} else {
			$this->myconn = $con;
		}
		
	}
	
	public function readCSV($csvFile) {
		$file_handle = fopen($csvFile, 'r');
		while (!feof($file_handle) ) {
			$line_of_text[] = fgetcsv($file_handle, 1024);
		}
		fclose($file_handle);
		return $line_of_text;
	}
	
	public function addError($error) {
		// array_push($this->errors, $error);
		$this->errors[] = $error;
	}
	
	public function writeErrors() {
		if (!empty($this->errors)) {
			$datetime = new DateTime();
			$dt = $datetime->format('Y-m-d h:m:s');
			
			$error_content = '\n\n======================';			
			$error_content .= $dt;
			$error_content = '\n======================\n';				
			$error_content .= implode('\n', $this->errors);
			
			file_put_contents($this->errFile, $error_content, FILE_APPEND | LOCK_EX);
		}
	}
	
	public function addCode($arr) {
				
		$found = $this->checkCode($arr[1]);
		
		if ( !empty($found) ) {			
			return $found;
		}
		else {
			// $insert = array();
			
			/*foreach ($arr as $key=>$value) {
				$insert[$key] = "' " . $this->myconn->real_escape_string($value) . " '";				
			}		
			*/
			
			$code_new = "'" . trim($this->myconn->real_escape_string($arr[1])) . "'";
			
			$insert_sql="INSERT INTO wj9ar_seals_codes (ordering,state,checked_out,checked_out_time,created_by,code,used_date,used,order_id,code_type) VALUES ('0','1','0','','0',$code_new,'','0','0','0')";
			
			if($this->myconn->query($insert_sql) === false) {
				trigger_error('Wrong SQL: ' . $insert_sql . ' Error: ' . $this->myconn->error, E_USER_ERROR);
			} else {
				$last_inserted_id = $this->myconn->insert_id;
				$affected_rows = $this->myconn->affected_rows;
				
				return $last_inserted_id;
			}
			
		}
		
		// return false;
	}
	
	public function checkCode($code) {
				
		$result =  $this->myconn->query("SELECT * FROM wj9ar_seals_codes WHERE code='".$code."' LIMIT 1");
		$x=null;
		
		if($result && !empty($result)) {
			while ($row = $result->fetch_object()) {
				foreach ( $row as $k=>$v ) {
					$x[$k] = $v;
				}		
			}
			return $x['id'];
			
		}
		else {
			return false;
		}		
		
	}

	public function addOrder($client) {
		
	}

}


?>