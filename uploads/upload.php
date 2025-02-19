<?php
if (isset($_REQUEST['input'])) {
    $input = stripslashes(trim(urldecode($_REQUEST['input'])));
	if (strpos($input, ' ')) {
		$new_input = str_replace(' ','-',$input);
		if (file_exists($new_input)) @unlink($new_input);
		rename($input, $new_input);
		$input = $new_input;
	}
	if (file_exists($input.'_output.txt')) {
		@unlink($input.'_output.txt');
	}
	if (isset($_REQUEST['output'])) {
		$output = stripslashes(trim(urldecode($_REQUEST['output'])));
	} else {
		$output = substr($input, 1 + strpos($input, '_'));
		$output = substr($output, 0, strrpos($output, '.')) . ".mp4";
	}
	$new_output = preg_replace( '/[^a-z0-9\.]+/', '-', strtolower( $output ) );
	$new_output = str_replace('--','-',$new_output);
	$output = $new_output;
}
if (file_exists('output.txt')) {
	@unlink('output.txt');
}
$uid	= isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$uuid	= isset($_REQUEST['uuid']) ? stripslashes($_REQUEST['uuid']) : '';
$sesnid	= isset($_REQUEST['sesnid']) ? intval($_REQUEST['sesnid']) : 0;
$max	= isset($_REQUEST['max']) ? intval($_REQUEST['max']) : 960;
$crf	= isset($_REQUEST['crf']) ? intval($_REQUEST['crf']) : 0;
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Shrink video</title>
	<style type="text/css">
		html, body {
			padding: 20px;
		}
	</style>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
	<script type="text/javascript">
		var uid = <?php echo $uid?>;
		var uuid = "<?php echo $uuid?>";
		var sesnid = <?php echo $sesnid?>;
		var max = <?php echo $max?>;
		var crf = <?php echo $crf?>;
		var output = "<?php echo $output?>";
		$(function () {
			var nexturl = '/show.php?output=uploads/' + $('#output').val() + '&uuid=' + uuid + '&uid=' + uid + '&sesnid=' + sesnid + '&max=' + max + '&crf=' + crf;
			var myProgress = setInterval(function () {
				$.get("progress.php?input="+$('#input').val(), function (data) {
					if (data === '') {
						$('#progress-string').html(data);
					} else if (data == 'done') {
						clearInterval(myProgress);
						$('#progress-string').html('<a href="' + nexturl + '"><span style="color:white;">done</span></a>');
						$('#progressbar').attr('aria-valuenow', data).css('width', `100%`);
						//console.log('post-interval', `$('#output').val()`);
						window.location.href = nexturl;
					} else {
						$('#progress-string').html(`${data}%`);
						$('#progressbar').attr('aria-valuenow', data).css('width', `${data}%`);
					}
				});
			}, 1000);//1000 milliseconds = 1 second
		});
	</script>
	<script>
		$(document).ready(function () {
			$('#make_smaller').on('submit', function (e) {
				e.preventDefault();
				$.ajax({
					type: "POST",
					url: 'run.php',
					data: {
						input: $('#input').val(),
						output: $('#output').val(),
						max: <?php echo $max;?>,
						crf: <?php echo $crf;?>
					},
				});
			});
			$('#make_smaller').submit();
		});
	</script>
	<link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col-12">
			<div class="card">
				<div class="card-body">
					<form id="make_smaller" method="post">
						<div class="form-row">
							<div class="form-group col-12">
								<label for="input">Input (<?php $filesize = round(filesize($input)/1024/1024,1); if ($filesize > 1024) echo round($filesize/1024,1).' GB'; else echo $filesize.' MB';?>)</label>
								<input type="text" class="form-control" name="input" id="input" aria-describedby="input"
									   placeholder="video.mp4"
									   value="<?php echo $input;?>">
							</div>
						</div>
						<div class="form-row">
								<label for="output">Output</label>
								<input type="text" class="form-control" name="output" id="output"
									   aria-describedby="output"
									   placeholder="out.mp4"
									   value="<?php echo $output;?>">
						</div>
						<div class="form-group" style="display:none;">
							<button type="submit" class="btn-green">Run</button>
						</div>
					</form>
					<div<?php include_once('progress.php'); ?>
					<div class="progress">
						<div id="progressbar" class="progress-bar bg-info" role="progressbar" aria-valuenow="0"
							 aria-valuemin="0" aria-valuemax="100"><span id="progress-string"></span></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
