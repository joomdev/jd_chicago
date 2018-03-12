<?php $local_file ='video/video.mp4';
			$size = filesize($local_file);
			header("Content-Type: video/mp4");
			header("Content-Length: ".$size);
			readfile($local_file); ?>