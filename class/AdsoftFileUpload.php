<?php 
//functions 
/*
 * NMT @ adsoft
 * 
 * to do: list config limits
 * upload progress?
 * translate phrases?
 * drop-down selectable folder to upload in?
 * browse folder contents & manipulate files?
 * mysql config save limits & register users
 * 
 */
class AdsoftFileUpload {
	// variables
	//public $upload_dir = "/var/www/vhosts/partyline.be/xml-proxyclick/new/"; //@partyline
	public $_upload_dir = 'uploaded/'; //@local
	public $_max_file_size = '10485760'; // maximum file size, in bytes
	public $_allowed_types = array('text/xml');  //file type as seen by apache
	public $_errorLogFile = 'upload_errors.html'; // existing file to be used as error logging
	
	/*
	public $_allowBadUploads;
	public $_allowBadUploads;
	
	protected function __construct() {
		$this->_allowBadUploads = mysqli select ...
	}
	*/
	
	function formatBytes($bytes, $precision = 2) {
		$units = array('B', 'Kb', 'Mb', 'Gb', 'Tb');
	
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
	
		// Uncomment one of the following alternatives
		$bytes /= pow(1024, $pow);
		// $bytes /= (1 << (10 * $pow));
	
		return round($bytes, $precision) . ' ' . $units[$pow];
	}

	function logError($errArray) {
		ob_start();
		var_dump($errArray);	
		$outStringVar = ob_get_contents();	
		
		$fp=fopen($this->_errorLogFile,'a+');
		
		fwrite($fp, $outStringVar.'<br />===================================<br />');
		fclose($fp);
		
		ob_end_clean();
	}


}
?>