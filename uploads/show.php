<?php
$uid	= isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$uuid	= isset($_REQUEST['uuid']) ? stripslashes($_REQUEST['uuid']) : '';
$sesnid	= isset($_REQUEST['sesnid']) ? intval($_REQUEST['sesnid']) : 0;
$show   = isset($_REQUEST['output']) ? stripslashes($_REQUEST['output']) : '';
$output = "pk000002.opt.mp4";
copy($show,$output);
?><!DOCTYPE html>
<html>
<head>
	<title>Video Uploaded</title>
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"
		integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<video src="<?php echo $output;?>" controls="" preload="auto" style="width: 100%;"></video>
				<h3 class="text-primary"><?php echo $output;?></h3>
			</div>
		</div>
	</div>
</body>
</html>
