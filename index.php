<?php 
	require_once "class/AdsoftFileUpload.php"; 
	$upload = new AdsoftFileUpload();
?>
<!DOCTYPE html>
<html>
<head>
<title>Proxyclick</title>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap-theme.min.css">

</head>

<body>
<div id="upload_form" class="hide">
	<form action="upload.php" target="hidden_iframe" enctype="multipart/form-data" method="post">
		<input type="file" multiple name="upload_files[]" id="upload_files">
	</form>
</div>

<div id="container">

	<div class="top"> 
		<div class="inner">
			<button id="select_files" onclick="Uploader.select();" class="custom-btn btn btn-primary btn-lg">Select Files</button>
		</div>	
		
		<div class="inner">
			<button id="submitBtn" disabled="disabled" onclick="Uploader.upload();" class="custom-btn btn btn-primary btn-lg">Upload</button>
		</div>
		
		<div class="clearer"></div>
		
		<div id="file-list" class="inner"></div>
		
		<div class="clearer"></div>
		
		<div id="wait" class="hide"><img border="0" src="img/upload-indicator.gif" alt=""></div>
	</div>
	
	<div class="clearer"></div>
	
	<div class="hide">
		<iframe name="hidden_iframe" id="hidden_iframe" class="hide"></iframe>
	</div>
	
	<div class="clearer"></div>
	
	<div class="uploaded_result"></div>

</div>

 <script type="text/javascript">
	var max_file_size = <?php echo $upload->_max_file_size; ?>,
 		selectedFiles = [],
 		allowed_types = <?php echo json_encode($upload->_allowed_types); ?>
 // var excluded = [];

jQuery().ready(function() {  
	jQuery('input[type=file]').each(function() {
        jQuery(this).val('');
    });
jQuery('#upload_files').val('');
jQuery('#submitBtn').prop('disabled', true);
});

var Uploader = (function () {        

        jQuery('#upload_files').on('change', function () {

            console.log('content changed');

        	if (jQuery(this).val() == '') {
        		jQuery('#submitBtn').prop('disabled', true);
           	} else {          		

           		var allFiles = this.files;
           		var fileHtml,
           			fileClass,
           			fileMsg,
           			validFile = true;
           		
           		selectedFiles = [];
           		selectedFiles.length = 0;   		
                  

	           	jQuery(allFiles).each(function() {
	           		fileClass = 'good';	 
	           		fileMsg = 'All good!';          		
	           		
	           		if (this.size > max_file_size) {
		            	fileClass = 'bad';		
		            	fileMsg = 'Maximum file size exceeded. ';  
		            	validFile = false;              	        		
	           		}  
	               	
	           		if (jQuery.inArray(this.type, allowed_types) === -1) {	           			
	           			fileClass = 'bad';	   
	           			validFile = false;   
	           			if (fileMsg != 'All good!') {     
	           				fileMsg += 'File type not supported. ';  
	           			}		
	           			else {
	           				fileMsg = 'File type not supported. '; 
	           			}	
	           		}	           		
	               	
	           		fileHtml = '<li data-message="'+fileMsg+'" class="'+ fileClass +'">'+ this.name +'</li>';		                
		        	selectedFiles.push(fileHtml);		                 
           		});           		      		

           		jQuery('#file-list').html('');
                
                if (validFile !== false) {
         			jQuery('#submitBtn').prop('disabled', false);
                }         
                else {
                	jQuery('#file-list').append('<div class="errors">You have selected some files that do not comply with the current settings.<br />Please press the Select button again and be sure not to choose those files again.<br />Please refer to the list below for details.<br />We disabled the upload button to save unneccessary server load.</div>');
                	jQuery('#file-list').append('<div class="clearer"></div>');                	  					
                }     
                jQuery('#file-list').append('<ul class="pre-upload">'+ selectedFiles.join("") + '</ul>');


                jQuery('ul.pre-upload li').each(function() {
                    var initialText = jQuery(this).text(),
                    	initialColor = jQuery(this).css('color');
                	jQuery(this)
                	 	.mouseenter(function() {							
                			 jQuery(this).text(jQuery(this).data('message'));
                			 jQuery(this).css('color', 'blue');
                			 })
                		 .mouseleave(function() {
                			jQuery( this ).text(initialText);
                			jQuery(this).css('color', initialColor);
                		 });			 

                });
                
                var el = jQuery("#file-list"),
                	curHeight = el.height(),
                	autoHeight = el.css('height', 'auto').height();
            	
                    el.height(curHeight).animate({ width: '400px', opacity: 1, height: autoHeight }, 400, function() {                          
                           el.css('height', 'auto');                            
                      }); 
                
          	}

            if (jQuery('.uploaded_result').css('display') != 'none') {
            	jQuery('.uploaded_result').fadeOut('fast');
            	jQuery('.uploaded_result').html('');
            }  			           
            
           
        });

        var fnSelect = function () {        	 
        //	jQuery('#submitBtn').prop('disabled', true);  	
            jQuery('#upload_files').trigger('click');
        }        

        var fnUpload = function () {
        	jQuery("#file-list").animate({ width: 0, height: 0,  opacity: 0});     
        	jQuery("#wait").removeClass('hide');        	
            jQuery('#upload_files').parent('form').submit();
        }

        var fnDone = function (data) {
        	
        	jQuery("#wait").addClass('hide');
        	jQuery('#upload_files').val("");
        	
        	jQuery('#submitBtn').prop('disabled', true);
        	
        	
            var data = JSON.parse(data);
            var files = data.files;
            var status = data.status;
         	var uploadResultStatus = '<div class="upload-result-status">Upload status: <span style="color: greenyellow; font-weight: bold;">'+status['uploaded']+'</span> uploaded, <span style="color: salmon; font-weight: bold;">'+status['failed']+'</span> failed</div>';   
            
          	jQuery('.uploaded_result').append(uploadResultStatus);
            jQuery('.uploaded_result').append('<ul class="upload-result">'+ files.join("") + '</ul>');
                        
            jQuery('.uploaded_result').fadeIn('fast');
        }

        return {
        	select: fnSelect,            
            upload: fnUpload,            
            done: fnDone
        }

    }());

  </script>
 </body>
</html>
