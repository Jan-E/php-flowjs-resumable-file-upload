<?php
set_time_limit(86400); // 1 day max_execution_time
if (isset($_REQUEST['input'])) {
    $input = stripslashes($_REQUEST['input']);
	if (file_exists($input)) {
		$output = stripslashes($_REQUEST['output']);
		$command = "ffmpeg -i ".$input." -filter_complex \"[0:v]setpts=PTS-STARTPTS[v0];[0:a]asetpts=PTS-STARTPTS[a0]\" -map [v0] -map [a0] -movflags +faststart -s 960x540 -aspect 16:9 -sws_flags bicubic -vcodec libx264 -acodec aac -ab 256k -ar 48000 -ac 2 -x264opts global_header=1:partitions=p8x8+b8x8+i8x8:level_idc=40:cabac=0:subq=3:qp_min=0:qp_max=51:qp_step=4:me=dia:subme=0:mixed_refs=0:me_range=16:chroma_me=1:trellis=0:8x8dct=0:cqm=flat:chroma_qp_offset=0:nr=0:keyint=30:min_keyint=15:scenecut=0:ratetol=1.0:qcomp=0.60:ip_factor=1.4:weightp=0:fast_pskip=1:frameref=1:bframes=0:mbtree=1:rc_lookahead=15:sliced_threads=0:threads=4 ".$output." -y 1> ".$input."_output.txt 2>&1";
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