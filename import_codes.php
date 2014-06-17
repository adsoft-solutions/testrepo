<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'class/');

include 'ImportCodes.php';

$csv = new ImportCodes();

$inputFileName = 'examples/altacodes.csv';  

try {
	$csv_read = $csv->readCSV($inputFileName);

} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

foreach($csv_read as $key=>$value){
	if ($key!=0) {		
		if ($value[4] == 'Alta') {
			try {
				$add = $csv->addCode($value);
				if ($add !== false) {
					continue;
				}
				else {
					throw new Exception ('Error adding code '.$value[1]);
				}
			}
			
			catch (Exception $e) {
				$csv->addError( $e->getMessage() );					
			}			
		}	
	}
}

$csv->writeErrors();

?>