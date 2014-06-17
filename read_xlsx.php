<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'class/');

include 'PHPExcel/IOFactory.php';
include 'ImportedOrders.php';


$inputFileName = 'examples/test.xlsx';  
try {
	$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
} catch(Exception $e) {
	die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
}

$orders = new ImportedOrders();

echo "<pre>";
$sheetData = $objPHPExcel->getActiveSheet();
$sheetDataArray = $sheetData->toArray(null,true,true,true);

foreach($sheetDataArray as $key=>$value){
	if ($key!=1) {
		foreach ($value as $k=>$v) {
			if ($k == 'F') {
				$exkey = $orders->addClient($v);
				if ($exkey) {
					$abc = $exkey;					
				}
				else {
					//$abc = $exkey;
				}				
			}					
		}	
	}


	print_r($value);
}


?>
<body>
</html>