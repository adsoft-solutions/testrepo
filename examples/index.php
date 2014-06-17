<?php require_once "config.php"; ?>

<!DOCTYPE html>
<html>
<head>
<title>Partyline.be Proxyclick</title>
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

<div id="top"> 
	<button onclick="Uploader.upload();" class="btn btn-primary btn-lg">Upload Files</button>
	<div id="wait" class="hide"><img border="0" src="img/upload-indicator.gif" alt=""></div>
</div>

<div class="clearer"></div>

<div class="hide">
	<iframe name="hidden_iframe" id="hidden_iframe" class="hide"></iframe>
</div>

<div class="clearer"></div>

<div id="uploaded_result">

</div>
 <script type="text/javascript">
 var max_file_size = <?php echo $max_file_size; ?>;
 // var excluded = [];


 var Uploader = (function () {        

        jQuery('#upload_files').on('change', function () {

            if (jQuery('#uploaded_result').css('display') != 'none') {
            	jQuery('#uploaded_result').fadeOut('fast');
            	jQuery('#uploaded_result').html('');
            }
			//	var allFiles = this.files;
            
        	// for (i in allFiles) {

            // if (allFiles[i].size > max_file_size) {
        	//	console.log(allFiles[i].size);
        	//	excluded.push(i);
           	//	 }        		  
        		  
           	//  }             
            
            jQuery("#wait").removeClass('hide');
            jQuery('#upload_files').parent('form').submit();
        });

        var fnUpload = function () {
        	// excluded = [];
        	// excluded.size = 0;
            jQuery('#upload_files').trigger('click');
        }

        var fnDone = function (data) {
			// console.log('excluded ids: '+excluded);
			
            var data = JSON.parse(data);
            var files = data.files;
            var status = data.status;
         	var uploadResultStatus = '<div class="upload-result-status">Upload status: <span style="color: green; font-weight: bold;">'+status['uploaded']+'</span> uploaded, <span style="color: red; font-weight: bold;">'+status['failed']+'</span> failed</div>';   
         //   if (data.error != "undefined") {
         //       jQuery('#uploaded_result').html(data['error']);
         //       jQuery('#upload_files').val("");
         //       jQuery("#wait").addClass('hide');
         //      return;
         //   }
         
          	jQuery('#uploaded_result').append(uploadResultStatus);
            jQuery('#uploaded_result').append('<ul class="upload-result">'+ files.join("") + '</ul>');
            jQuery('#upload_files').val("");
            jQuery("#wait").addClass('hide');
            jQuery('#uploaded_result').fadeIn('fast');
        }

        return {
            upload: fnUpload,
            done: fnDone
        }

    }());

  </script>
 </body>
</html>
