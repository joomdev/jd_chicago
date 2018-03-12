<?php

/**
 * @copyright	Copyright (c) 2016 jd_video. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
$doc = JFactory::getDocument();
$app      = JFactory::getApplication();
$video_path = $params->get('video_path') ;
$youtube_status = $params->get('youtube_status');
$youtube_url = $params->get('youtube_embed');
$full_width = $params->get('full_width');
$video_height = $params->get('video_height');
$description = $params->get('description');
$url = JFactory::getURI();
$cssurl = $url.'modules/mod_jd_video/css/';
$jsurl = $url.'modules/mod_jd_video/js/';
$videourl = $url.'modules/mod_jd_video/video/';
if($full_width == 1)
{
	$class = 'class="fullwidth"';
	 
}
else {
	$class = '';
}
?>
<div <?php echo $class;?> style="height:<?php echo $video_height.'px';?>">

<?php

			if(file_exists($video_path))
			{
			$my_file = 'video.php';
			$handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
			$data = '<?php $local_file ='."'".$video_path."'".';
			$size = filesize($local_file);
			header("Content-Type: video/mp4");
			header("Content-Length: ".$size);
			readfile($local_file); ?>';
			fwrite($handle, $data);
		?>
	
		<video autoplay loop ><source src="video.php" type="video/mp4" /> <source src="video.php" type="video/ogg" /> <source src="video.php" type="video/webm" /> Your browser does not support the video tag. I suggest you upgrade your browser.</video>
		<?php	
			}
			else {
				echo 'No Video Available';
			}
		

?>
</div>

<div class="video-description">
<?php echo $description;?>
</div>