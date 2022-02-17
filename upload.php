<?php
set_time_limit(3600); // 1 hour max_execution_time
?><!DOCTYPE html>
<html>

<head>
	<title>Resumable Video Upload</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="/css/bootstrap.min.css">
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-8 col-sm-offset-2 text-center">
				<h3 class="text-primary">Upload Video</h3>
					<p>
						<!-- Upload button -->
						<button type="button" id="upbrowse" class="btn btn-primary"><span class="glyphicon glyphicon-upload"></span> Browse Files</button>
						<button type="button" id="upToggle" class="btn btn-default"><span class="glyphicon glyphicon-play"></span> Pause OR Continue</button>
					
						<!-- Upload file listing -->
						<div id="uplist"></div>
					</p>
			</div>
		</div>
<?php
$uid	= isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$uuid	= isset($_REQUEST['uuid']) ? stripslashes($_REQUEST['uuid']) : '';
$sesnid	= isset($_REQUEST['sesnid']) ? intval($_REQUEST['sesnid']) : 0;
if (isset($_REQUEST['show'])) {
	$show = stripslashes(trim(urldecode($_REQUEST['show'])));
	$showname = substr($show, 1 + strrpos($show, '/'));
?>
		<div class="row">
			<div class="col-sm-12 text-center">
				<video src="<?php echo $show;?>" controls="" preload="auto" style="width: 100%;"></video>
				<h3 class="text-primary"><?php echo $showname;?></h3>
			</div>
		</div>
<?php } ?>
	</div>

	<!-- Check https://cdnjs.com/libraries/flow.js for latest version -->
	<!-- GitHub Link https://github.com/flowjs/flow.js/ -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/flow.js/2.14.1/flow.min.js"></script>
	<script>
		window.addEventListener("load", function () {
			/* Setup new flow object and server php file here */
			var uid = <?php echo $uid?>;
			var uuid = "<?php echo $uuid?>";
			var sesnid = <?php echo $sesnid?>;
			var max = <?php echo $max?>;
			var uniqid = "<?php $uniq = uniqid(); echo $uniq?>";
			var flow = new Flow({
				target: 'resumable.php<?php echo "?uniqid=".$uniq;?>',
				chunkSize: 4 * 1024 * 1024, /** Whole file is broken in chunks of 4MB */
				singleFile: true
			});

			if (flow.support) {
				/* Browse button */
				flow.assignBrowse(document.getElementById('upbrowse'));
				/* Incase if you want to support Drop zone box then uncomment the following */
				// flow.assignDrop(document.getElementById('updrop'));

				/* Action to perform when the file is added */
				flow.on('fileAdded', function (file, event) {
					let fileslot = document.createElement("div");
					fileslot.id = file.uniqueIdentifier;
					fileslot.innerHTML = `${file.name} (${file.size}) - <strong>0%</strong>`;
					document.getElementById("uplist").appendChild(fileslot);
				});

				/** Any action soon as the file is submitted */
				flow.on('filesSubmitted', function (array, event) {
					//console.log(array, event);
					flow.upload();
				});

				/** Action to perform while the file is being uploaded ie in progress state */
				flow.on('fileProgress', function (file, chunk) {
					//console.log(file, chunk);
					let progress = (chunk.offset + 1) / file.chunks.length * 100;
					progress = progress.toFixed(2) + "%";

					let fileslot = document.getElementById(file.uniqueIdentifier);
					fileslot = fileslot.getElementsByTagName("strong")[0];
					fileslot.innerHTML = progress;
				});

				/** When the uploading on the file is completed */
				flow.on('fileSuccess', function (file, message, chunk) {
					//console.log(file, message, chunk);
					var uploadFileName = uniqid + '_' + `${file.name}`;
					console.log(uploadFileName);
					let fileslot = document.getElementById(file.uniqueIdentifier);
					fileslot = fileslot.getElementsByTagName("strong")[0];
					fileslot.innerHTML = '<a href="/uploads/upload.php?input=' + uploadFileName + '&uuid=' + uuid + '&uid=' + uid + '&sesnid=' + sesnid + '&max=' + max + '">DONE - Please wait</a>';
					var delayInMilliseconds = 5000; //5 seconds
					setTimeout(function() {
						//your code to be executed after 1 second
						window.location.href = '/uploads/upload.php?input=' + uploadFileName + '&uuid=' + uuid + '&uid=' + uid + '&sesnid=' + sesnid + '&max=' + max;
					}, delayInMilliseconds);
				});

				/** Action to perform when an error occurs during file upload */
				flow.on('fileError', function (file, message) {
					console.log(file, message);
					let fileslot = document.getElementById(file.uniqueIdentifier);
					fileslot = fileslot.getElementsByTagName("strong")[0];
					fileslot.innerHTML = "ERROR";
				});

				/** Pause or Unpause the upload process */
				document.getElementById("upToggle").addEventListener("click", function () {
					if (flow.isUploading()) { flow.pause(); }
					else { flow.resume(); }
				});
			}
		});
	</script>
</body>

</html>
