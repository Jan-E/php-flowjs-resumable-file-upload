<?php
$uid	= isset($_REQUEST['uid']) ? intval($_REQUEST['uid']) : 0;
$uuid	= isset($_REQUEST['uuid']) ? stripslashes($_REQUEST['uuid']) : '';
$sesnid	= isset($_REQUEST['sesnid']) ? intval($_REQUEST['sesnid']) : 0;
$max	= isset($_REQUEST['max']) ? intval($_REQUEST['max']) : 960;
$crf	= isset($_REQUEST['crf']) ? intval($_REQUEST['crf']) : 0;
$input  = isset($_REQUEST['output']) ? stripslashes($_REQUEST['output']) : '';
$frame	= isset($_REQUEST['frame']) ? max(30,intval($_REQUEST['frame'])) : 30;
$width	= 960;
$height = 720;
$duration = 3600; // default value
$scale = 'scale=960:-2,setsar=1:1';
if(extension_loaded('ffmpeg')) {
	$ffmpegInstance = new ffmpeg_movie($input);
	if ($ffmpegInstance) {
		$width = $ffmpegInstance->getFrameWidth();
		$height = $ffmpegInstance->getFrameHeight();
		$aspect = $ffmpegInstance->getPixelAspectRatio();
		$length = $ffmpegInstance->getDuration();
		if ($length) $duration = $length;
	}
}
$duration = ceil($duration);
$file_size = filesize($input);
$ticksstring = '';
$nid = '';
$fid = '';
if ($duration && $file_size) {
	$ticksstring = "https://dev3.sessionportal.net/tfrticks.php?uuid=".$uuid."&uid=".$uid."&sesnid=".$sesnid."&json=1&ff_resolution_width=".$width."&ff_resolution_height=".$height."&ff_duration=".$duration."&ff_compressed_file_size=".$file_size."&ff_uploadtool=flowjs";
	$ticksj = @file_get_contents($ticksstring);
	if ($ticksj) {
		$ticks  = @json_decode($ticksj, true);
		$success = isset($ticks['success']) ? $ticks['success'] : 0;
		if ($success) {
			$output = isset($ticks['filename']) ? $ticks['filename'] : NULL;
			$filesize = isset($ticks['filesize']) ? $ticks['filesize'] : NULL;
			$fid = isset($ticks['fid']) ? $ticks['fid'] : 0;
			$nid = isset($ticks['nid']) ? $ticks['nid'] : 0;
			copy($_SERVER['DOCUMENT_ROOT'] . '/' . $input, $_SERVER['DOCUMENT_ROOT'] . '/wmpub/pk/' . $output);
		}
	}
}
?><!DOCTYPE html>
<html>
<head>
	<title>Video Uploaded</title>
	<!-- <?php echo $ticksstring;?> -->
	<link rel="stylesheet" href="/css/bootstrap.min.css">
</head>
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<h3 class="text-primary"><a href="https://dev3.sessionportal.net/group/<?php echo $sesnid;?>/content/add/group_node:video_node?edit[entity_id][widget][0][target_id]=<?php echo $nid;?>">Back to the session</a></h3>
				<video src="<?php echo isset($output) ? 'wmpub/pk/'.$output : $input;?>" controls="" preload="auto" style="width: 100%; max-width: <?php echo $max?>px; max-height: <?php echo $max?>px;"></video>
				<h3 class="text-primary"><?php echo isset($output) ? $output : str_replace('uploads/','',$input);?>  (<?php $filesize = round(filesize($input)/1024/1024,1); echo $filesize.' MB';?>)</h3>
			</div>
		</div>
	</div>
</body>
<?php
$command = "rsync --progress -tr --append /home/admin/domains/flow.kleinestappen.nl/public_html/wmpub/pk/* storage4@pcx4ipv4.elijst.nl:/cygdrive/c/wmpub/pk/";
shell_exec($command);
?></html>
