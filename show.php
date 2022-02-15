<?php
$uid	= isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$uuid	= isset($_REQUEST['uuid']) ? stripslashes($_REQUEST['uuid']) : '';
$sesnid	= isset($_REQUEST['sesnid']) ? intval($_REQUEST['sesnid']) : 0;
$input  = isset($_REQUEST['output']) ? stripslashes($_REQUEST['output']) : '';
$frame	= isset($_REQUEST['frame']) ? max(30,intval($_REQUEST['frame'])) : 30;
$width	= 960;
$height = 720;
if (extension_loaded('ffmpeg')) {
	$ffmpegInstance = new ffmpeg_movie($input);
	$ffmpegFrame = $ffmpegInstance->getFrame($frame);
	$width = $ffmpegInstance->getFrameWidth();
	$height = $ffmpegInstance->getFrameHeight();
}
$ticksj = file_get_contents("https://dev3.sessionportal.net/tfrticks.php?uid=".$uid."&json=1&ff_resolution_width=".$width."&ff_resolution_height=".$height."&ff_uploadtool=flowjs");
$ticks  = json_decode($ticksj, true);
$success = isset($ticks['success']) ? $ticks['success'] : 0;
$output = isset($ticks['filename']) ? $ticks['filename'] : NULL;
$filesize = isset($ticks['filesize']) ? $ticks['filesize'] : NULL;
$fid = isset($ticks['fid']) ? $ticks['fid'] : 0;
$nid = isset($ticks['nid']) ? $ticks['nid'] : 0;
copy($_SERVER['DOCUMENT_ROOT'] . '/' . $input, $_SERVER['DOCUMENT_ROOT'] . '/wmpub/pk/' . $output);
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
				<h3 class="text-primary"><a href="https://dev3.sessionportal.net/group/<?php echo $sesnid;?>/content/add/group_node%3Avideo_node?edit[entity_id][widget][0][target_id]=<?php echo $nid;?>">Back to the session</a></h3>
				<video src="wmpub/pk/<?php echo $output;?>" controls="" preload="auto" style="width: 100%;"></video>
				<h3 class="text-primary"><?php echo $output;?></h3>
			</div>
		</div>
	</div>
</body>
<?php
$command = "rsync --progress -tr --append /home/admin/domains/flow.kleinestappen.nl/public_html/wmpub/pk/* storage4@pcx4ipv4.elijst.nl:/cygdrive/c/wmpub/pk/";
shell_exec($command);
?></html>
