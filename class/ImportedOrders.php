<?php 
class ImportedOrders {
	public $persons = array();

	
	public function __construct(){
		$this->persons = array();		
	}
	
	public function addClient($client) {
		$found = $this->findClient($client);
		
		if ( !empty($found) ) {			
			return $found;
		}
		else {
			array_push($this->persons, array('person_name' => trim($client)));			
			return end($this->persons);
		}
		
		return false;
	}
	
	public function findClient($client) {
		
		foreach ($this->persons as $key => $val) {
			if ($val['person_name'] == $client) {
				return $key;
			}
		}
		
		return false;
		
	}

	public function addOrder($client) {
		
	}

}


?>