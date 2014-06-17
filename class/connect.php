<?php 

/*
var $db = 'id00723_partyline';
var $host = 'localhost';
var $user = 'partyline';
var $pass = 'yzaputada';
*/


class createCon  {
	protected $host = 'localhost';
	protected $user = 'root';
	protected $pass = '';
	protected $db = 'teste';
	protected $myconn;
	
	function connect() {
		$con = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
		if (!$con) {
			die('Could not connect to database!');
		} else {
			$this->myconn = $con;
		}
			return $this->myconn;
	}

	function close() {
		mysqli_close($this->myconn);
		echo 'Connection closed!';
	}

}


?>