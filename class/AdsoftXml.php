<?php
/*
protected $db = 'id00723_partyline';
protected $host = 'localhost';
protected $user = 'partyline';
protected $pass = 'yzaputada';
*/

class AdsoftXml {
	
	public $_saveFile; 
	public $_saveBackupFile;
	
	protected $host = 'localhost';
	protected $user = 'root';
	protected $pass = '';
	protected $db = 'teste';
	protected $myconn;	
		
	function getConnection() {
		return $this->myconn;
	}
	
	function __construct()
	{
		$this->_saveFile = date("YmdHis").".xml";
		
		$con = mysqli_connect($this->host, $this->user, $this->pass, $this->db);
		if (!$con) {
			die('Could not connect to database!');
		} else {
			$this->myconn = $con;
		}
		
		/* LIVE:
		$this->_saveFile = str_replace("/httpdocs","",__DIR__)."/anon_ftp/incoming/".date("YmdHis").".xml";
		$this->_saveBackupFile = str_replace("/httpdocs","",__DIR__)."/xml-backup/".date("YmdHis").".xml";
		*/		
	}
	
	
	function Unaccent($string)	{
	    if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false)
	    {
	        $string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
	    }
			$string = htmlspecialchars($string, ENT_QUOTES); 
			$string = str_replace('&','&amp;',$string);

    return $string;
	}

	
	function array_flatten($array)	{ 
		if (!is_array($array)){ 
			return FALSE; 
		} 
		
		$result_xyz = array(); 
		
		foreach ($array as $key => $value) { 
			if (is_array($value)) { 
				$result_xyz = array_merge($result_xyz, $this->array_flatten($value)); 
			} 
			else { 
				$result_xyz[$key] = $value; 
			} 
		} 
		return $result_xyz; 
	}

	
	function getCountry($id) {
		// $mysqli = new mysqli("localhost", "partyline", "yzaputada", "id00723_partyline");
		$result2 = $this->getConnection()->query("SELECT * FROM jos_virtuemart_countries WHERE virtuemart_country_id='".$id."' LIMIT 1");
		$x=null;
		
		if($result2)
		{
	        while ($row = $result2->fetch_object())
	        {
		        foreach ( $row as $key2=>$value2 )
		        {
		        	$x[$key2] = $value2;
		        }
		        
		    }
		//    $this->getConnection()->next_result();
		}
		return  $x['country_2_code'];
	}
	
	function getDeliveryDate($orderId,$dtype)	{
	//	$mysqli = new mysqli("localhost", "partyline", "yzaputada", "id00723_partyline");
		$result3_ = $this->getConnection()->query("SELECT * FROM jos_virtuemart_delivery_date WHERE virtuemart_order_id='".$orderId."' LIMIT 1");
		$x=null;
		
		if($result3_)
		{
	        while ($row = $result3_->fetch_object())
	        {
		        foreach ( $row as $key3_=>$value3_ )
		        {
		        	$x[$key3_] = $value3_;
		        }
		        
		    }
		//    $this->getConnection()->next_result();
		}
		return  $x[$dtype];
	}	
	
	
}
	?>