<?php
set_time_limit(86400); // 1 day max_execution_time
$duration = 0;
if (isset($_REQUEST['input'])) {
    $input = stripslashes($_REQUEST['input']);
	$max = isset($_REQUEST['max']) ? intval($_REQUEST['max']) : 960;
	$crf = 28;
	if ($max <= 640) $crf = 26;
	if ($max <= 448) $crf = 26;
	$crf = isset($_REQUEST['crf']) && intval($_REQUEST['crf']) ? intval($_REQUEST['crf']) : $crf;
	$scale = 'scale='.$max.':-2,setsar=1:1';
	$rotated = false;
	@exec("mediainfo $input 2>".$input."_mediainfo.txt", $str);
	$fp = @fopen($input.'_mediainfo.txt','w');
	while (list($nr,$text) = each($str)) {
		if($fp) fwrite($fp, $text."\n");
		/*	deal with rotated videos (from my Sony Xperia XZ2 Compact)
			 Width                                    : 1 920 pixels
			 Height                                   : 1 080 pixels
			 Display aspect ratio                     : 16:9
			 Rotation                                 : 90°
			 */
		if (preg_match('/Width.*: ([0-9 ]+) pixels/', $text)) {
			$pattern = '/Width.*: ([0-9 ]+) pixels/i';
			$replacement = '${1}';
			$width = str_replace(' ','',preg_replace($pattern, $replacement, $text));
		}
		if (preg_match('/Height.*: ([0-9 ]+) pixels/', $text)) {
			$pattern = '/Height.*: ([0-9 ]+) pixels/i';
			$replacement = '${1}';
			$height = str_replace(' ','',preg_replace($pattern, $replacement, $text));
		}
		if (preg_match('/Rotation.*: 90.*/', $text)) {
			$scale = 'scale=-2:'.$max.',setsar=1:1';
			$rotated = true;
		}
	}
	if($fp) fclose($fp);
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
	if ($height && $width) {
		if ($rotated) {
			$tmp = $height;
			$height = $width;
			$width = $tmp;
			unset($tmp);
		}
		if ($width > $height) {
			// landscape
			if (!$aspect) $aspect = 1;
			$scaledwidth = min($max, $width);
			$scalefactor = $scaledwidth / $width;
			$scaledheight = round($scalefactor * $height / $aspect);
			$scale = 'scale='.$scaledwidth.':-2,setsar=1:1';
		} else {
			// portrait
			$scaledheight = min($max, $height);
			$scalefactor = $scaledheight / $height;
			$scaledwidth = $scalefactor * $width;
			$scale = 'scale=-2:'.$scaledheight.',setsar=1:1';
		}
	}
	if (file_exists($input)) {
		$output = stripslashes($_REQUEST['output']);
		$command = "ffmpeg -i ".$input." -crf ".$crf." -movflags +faststart -vf ".$scale." -sws_flags bicubic -vcodec libx264 -acodec aac -x264opts global_header=1:partitions=p8x8+b8x8+i8x8:level_idc=40:cabac=0:subq=3:qp_min=0:qp_max=51:qp_step=4:me=dia:subme=0:mixed_refs=0:me_range=16:chroma_me=1:trellis=0:8x8dct=0:cqm=flat:chroma_qp_offset=0:nr=0:keyint=30:min_keyint=5:scenecut=0:ratetol=1.0:qcomp=0.60:ip_factor=1.4:weightp=0:fast_pskip=1:frameref=1:bframes=0:mbtree=1:rc_lookahead=15:sliced_threads=0:threads=4 ".$output." -y 1> ".$input."_output.txt 2>&1";
		$fp = @fopen($input.'_ffmpeg.txt','w');
		if($fp) fwrite($fp, $command);
		if($fp) fclose($fp);
		shell_exec($command);
		if (file_exists($input.'_output.txt')) {
			$fp = @fopen($input.'_output.txt','w');
			if($fp) fwrite($fp, 'done');
			if($fp) fclose($fp);
		}
	} else {
		echo "Input file not found";
		exit;
	}
} else {
	echo "Did not come from the form";
	exit;
}
?>