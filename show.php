<?php
$ticksdom = "https://dev3.sessionportal.net";
if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') $ticksdom = "http://localhost.d9.sessionportal.net";
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
	$ffmpegInstance = new ffmpeg_movie(__DIR__ . '/' . $input);
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
	$ticksstring = $ticksdom."/tfrticks.php?uuid=".$uuid."&uid=".$uid."&sesnid=".$sesnid."&json=1&ff_resolution_width=".$width."&ff_resolution_height=".$height."&ff_duration=".$duration."&ff_compressed_file_size=".$file_size."&ff_uploadtool=flowjs";
	$ticksj = @file_get_contents($ticksstring);
	if ($ticksj) {
		$ticks  = @json_decode($ticksj, true);
		$success = isset($ticks['success']) ? $ticks['success'] : 0;
		if ($success) {
			$output = isset($ticks['filename']) ? $ticks['filename'] : NULL;
			$filesize = isset($ticks['filesize']) ? $ticks['filesize'] : NULL;
			$fid = isset($ticks['fid']) ? $ticks['fid'] : 0;
			$nid = isset($ticks['nid']) ? $ticks['nid'] : 0;
			copy(__DIR__ . '/' . $input, __DIR__ . '/wmpub/pk/' . $output);
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
<?php
// previously: https://dev3.sessionportal.net/group/{$sesnid}/content/add/group_node:video_node?edit[entity_id][widget][0][target_id]={$nid}
// tfrticks.php?uuid=bfe05a9e-4a82-4e16-83c6-fe2eef8368fc&uid=5&fn=pk000097.opt.mp4&sesnid=18&nid=134&ff_upload_success=1&redirect=1
$filename = isset($output) ? $output : str_replace('uploads/','',$input);
$newticksstring = $ticksdom."/tfrticks.php?uuid=".$uuid."&uid=".$uid."&fn=".$filename."&sesnid=".$sesnid."&nid=".$nid."&ff_upload_success=1&redirect=1";
?>
<!-- <?php echo $newticksstring;?> -->
<body>
	<div class="container">
		<div class="row">
			<div class="col-sm-12 text-center">
				<h3 class="text-primary"><a href="<?php echo $newticksstring;?>">Back to the session</a></h3>
				<video src="<?php echo isset($output) ? 'wmpub/pk/'.$output : $input;?>" controls="" preload="auto" style="width: 100%; max-width: <?php echo $max?>px; max-height: <?php echo $max?>px;"></video>
				<h3 class="text-primary"><?php echo $filename;?>  (<?php $filesize = round(filesize($input)/1024/1024,1); echo $filesize.' MB';?>)</h3>
			</div>
		</div>
	</div>
</body>
<?php
if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
	$command = "copy wmpub\\pk\\{$filename} \\wmpub\\pk\\";
} else {
	$command = "rsync --progress -tr --append /home/admin/domains/flow.kleinestappen.nl/public_html/wmpub/pk/* storage4@pcx4ipv4.elijst.nl:/cygdrive/c/wmpub/pk/";
}
shell_exec($command);
?></html>
