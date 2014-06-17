<?php require_once "class/AdsoftFileUpload.php"; 

$upload = new AdsoftFileUpload();

if (isset ( $_POST )) {
	
	$failed = 0;
	$success = 0;	
	
	$result = array();
	$uploaded = array();
		
	if (isset ($_FILES['upload_files']) && $_FILES['upload_files']['error'] != 0) {
		
		foreach ($_FILES['upload_files']['tmp_name'] as $key => $value) {
			
			$file_name = $_FILES['upload_files']['name'][$key];
			$file_size = $_FILES['upload_files']['size'][$key];
			$file_type = $_FILES['upload_files']['type'][$key];
			$file_tmp_name = $_FILES['upload_files']['tmp_name'][$key];
			$file_error = $_FILES['upload_files']['error'][$key];
			
			if (!file_exists (  $upload->_upload_dir . $file_name )) {
				if ($file_size > $upload->_max_file_size) {
					$failed++;
					$uploaded[$key] = '<li class=\"skipped\">' . $file_name . '  <span>not uploaded<\/span> (file size exceeds limit - <b>' .  $upload->formatBytes( $file_size ) . '</b> > <b>' .  $upload->formatBytes (  $upload->_max_file_size ) . '</b>)<\/li>';
					continue;
				}				
				
				if (in_array($file_type,  $upload->_allowed_types)) {
					
					if (!move_uploaded_file ( $value,  $upload->_upload_dir . $file_name )) {
						
						$error = array();

						$error['message'] = "Failed to write uploaded file to destination folder\n";
						
						$error['file']['name'] = $file_name;
						$error['file']['size'] = $file_size;
						$error['file']['type'] = $file_type;
						$error['file']['tmp_name'] = $file_tmp_name;
						$error['file']['error'] = $file_error;			
						$error['env']['destination_folder'] = $upload_dir;		
						$error['env']['remote_ip'] = $_SERVER['REMOTE_ADDR'];
						$error['env']['datenow'] = date("Y-m-d H:i:s");
						$error['env']['referer'] = $_SERVER['HTTP_REFERER'];						
												
						 $upload->logError($error);												
						
						$failed++;
						$uploaded[$key] = '<li class=\"failed\">' . $file_name . ' <span>not uploaded<\/span> (unknown error, please report to admin)<\/li>';					
					} else {
						$success++;
						$uploaded[$key] = '<li class=\"success\">' . $file_name . ' <span>uploaded!<\/span><\/li>';
					}
				} else {
					$failed++;
					$uploaded[$key] = '<li class=\"skipped\">' . $file_name . ' <span>not uploaded<\/span> (file type not supported)<\/li>';
				}			
				
			} else {
				$failed++;
				$uploaded[$key] = '<li class=\"skipped\">' . $file_name . ' <span>not uploaded<\/span> (file already exists in folder)<\/li>';
			}
		}
	}
	
	$result['files'] = $uploaded;
	$result['status'] = array('uploaded' => $success, 'failed' => $failed);
	
	
?>

<html>
<body>
	<script type="text/javascript">
		window.parent.Uploader.done('<?php echo json_encode($result); ?>');
	</script>
</body>
</html>

<?php  } ?>
