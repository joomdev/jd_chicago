<?php
/**
* ChronoCMS version 1.0
* Copyright (c) 2012 ChronoCMS.com, All rights reserved.
* Author: (ChronoCMS.com Team)
* license: Please read LICENSE.txt
* Visit http://www.ChronoCMS.com for regular updates and information.
* this action is a remake of the original image resizing plugin for Chronoforms V3.x by: Emmanuel Danan - www.vistamedia.fr - emmanuel AT vistamedia DOT fr
**/
namespace GCore\Admin\Extensions\Chronoforms\Actions\ImageResize;
/* @copyright:ChronoEngine.com @license:GPLv2 */defined('_JEXEC') or die('Restricted access');
defined("GCORE_SITE") or die;
Class ImageResize extends \GCore\Admin\Extensions\Chronoforms\Action{
	static $title = 'Image Resize';
	static $group = array('utilities' => 'Utilities');

	var $defaults = array(
		'photo' => '',
		'delete_original' => 0,
		'quality' => 90,
		'big_directory' => '',
		'big_image_use' => '1',
		'big_image_prefix' => '',
		'big_image_suffix' => '_big',
		'big_image_height' => '300',
		'big_image_width' => '400',
		'big_image_r' => '255',
		'big_image_g' => '255',
		'big_image_b' => '255',
		'big_image_method' => '0',
		'med_directory' => '',
		'med_image_use' => '0',
		'med_image_prefix' => '',
		'med_image_suffix' => '_med',
		'med_image_height' => '300',
		'med_image_width' => '400',
		'med_image_r' => '255',
		'med_image_g' => '255',
		'med_image_b' => '255',
		'med_image_method' => '0',
		'small_image_use' => '0',
		'small_directory' => '',
		'small_image_prefix' => '',
		'small_image_suffix' => '_small',
		'small_image_height' => '300',
		'small_image_width' => '400',
		'small_image_r' => '255',
		'small_image_g' => '255',
		'small_image_b' => '255',
		'small_image_method' => '0'
	);
	
	function execute(&$form, $action_id){
		$config =  $form->actions_config[$action_id];
		$config = new \GCore\Libs\Parameter($config);
		
		//set the images path
		$upload_path = $config->get('upload_path', '');
		if(!empty($upload_path)){
			$upload_path = str_replace(array("/", "\\"), DS, $upload_path);
			if(substr($upload_path, -1) == DS){
				$upload_path = substr_replace($upload_path, '', -1);
			}
			$upload_path = $upload_path.DS;
			$config->set('upload_path', $upload_path);
		}else{
			$upload_path = \GCore\C::ext_path('chronoforms', 'front').'uploads'.DS.$form->form['Form']['title'].DS;
		}
		
		$image_file_name = $config->get('photo', '');
		if(strpos($image_file_name, ',') !== false){
			$image_file_names = explode(',', $image_file_name);
		}else{
			$image_file_names = array($image_file_name);
		}
		
		foreach($image_file_names as $image_file_name){
			//stop if the field name is not set or if the file data doesn't exist
			//if((strlen($image_file_name) == 0) || !isset($form->data[$image_file_name]) || !isset($form->files[$image_file_name]['path'])){
			if((strlen($image_file_name) == 0) || !isset($form->files[$image_file_name])){
				continue;
			}
			
			if($form->files[$image_file_name] === array_values($form->files[$image_file_name])){
				//array of files
				$reset = false;
			}else{
				$form->files[$image_file_name] = array($form->files[$image_file_name]);
				$reset = true;
			}
			foreach($form->files[$image_file_name] as $k => $image){
				// Common parameters		
				$photo = $image['name'];//$form->data[$image_file_name];
				$filein = $image['path'];			
				
				$file_info = pathinfo($filein);
				
				$form->debug[$action_id][self::$title][] = $form->files[$image_file_name][$k]['thumb_big'] = $this->processSize('big', $form, $config, $form->actions_config[$action_id], $photo, $filein, $upload_path, $file_info);
				// treatment of the medium image
				$form->debug[$action_id][self::$title][] = $form->files[$image_file_name][$k]['thumb_med'] = $this->processSize('med', $form, $config, $form->actions_config[$action_id], $photo, $filein, $upload_path, $file_info);

				// treatment of the small image
				$form->debug[$action_id][self::$title][] = $form->files[$image_file_name][$k]['thumb_small'] = $this->processSize('small', $form, $config, $form->actions_config[$action_id], $photo, $filein, $upload_path, $file_info);

				if($config->get('delete_original')){
					unlink($filein);
				}
			}
			if($reset){
				$form->files[$image_file_name] = $form->files[$image_file_name][0];
			}
		}
	}
	
	function processSize($size = 'big', $form, $config, $actiondata, $photo, $filein, $upload_path, $file_info){
		$quality = $config->get('quality', 90);
		$dir = '';
		if($config->get($size.'_directory', '')){
			$dir .= $config->get($size.'_directory', '');
		} else {
			$dir .= $upload_path;
		}
		// add a final slash if needed
		if(substr($dir, -1) != DS){
			$dir .= DS;
		}

		$fileout 			= $dir.$config->get($size.'_image_prefix', '').str_replace('.'.$file_info['extension'], '', $photo).$config->get($size.'_image_suffix', '').'.'.$file_info['extension'];
		$crop 				= $config->get($size.'_image_method', 0);
		$imagethumbsize_w 	= $config->get($size.'_image_width', 400);
		$imagethumbsize_h 	= $config->get($size.'_image_height', 300);
		$red				= $config->get($size.'_image_r', 255);
		$green				= $config->get($size.'_image_g', 255);
		$blue				= $config->get($size.'_image_b', 255);
		$use				= $config->get($size.'_image_use', 0);
		
		if($size == 'big'){
			$use = true;
		}
		if($use){
			if($crop){
				$this->resizeThenCrop($filein, $fileout, $imagethumbsize_w, $imagethumbsize_h, $red, $green, $blue, $quality);
			}else{
				$this->resize($filein, $fileout, $imagethumbsize_w, $imagethumbsize_h, $red, $green, $blue, $quality);
			}
			return $config->get($size.'_image_prefix', '').str_replace('.'.$file_info['extension'], '', $photo).$config->get($size.'_image_suffix', '').'.'.$file_info['extension'];
		}
		return null;
	}
	
	function resizeThenCrop( $filein, $fileout, $imagethumbsize_w, $imagethumbsize_h, $red, $green, $blue, $quality )
	{
        // Cacul des nouvelles dimensions
        list($width, $height) = getimagesize($filein);
        //$new_width  = $width * $percent;
        //$new_height = $height * $percent;

        if ( preg_match("/.jpg/i", "$filein") || preg_match("/.jpeg/i", "$filein") ) {
            $format = 'image/jpeg';
        } elseif ( preg_match("/.gif/i", "$filein") ) {
            $format = 'image/gif';
        } else if( preg_match("/.png/i", "$filein") ) {
            $format = 'image/png';
        }

        switch($format) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filein);
                break;
            case 'image/gif';
                $image = imagecreatefromgif($filein);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filein);
                break;
        }

        $width  = $imagethumbsize_w ;
        $height = $imagethumbsize_h ;
        list($width_orig, $height_orig) = getimagesize($filein);

        if ( $width_orig < $height_orig ) {
            $height = ($imagethumbsize_w / $width_orig) * $height_orig;
        } else {
            $width  = ($imagethumbsize_h / $height_orig) * $width_orig;
        }

        if ( $width < $imagethumbsize_w ) {
            // If the image width is less than the thumbnail
            $width  = $imagethumbsize_w;
            $height = ($imagethumbsize_w / $width_orig) * $height_orig;
        }

        if ( $height < $imagethumbsize_h ) {
            // If the image height is less than the thumbnail

            $height = $imagethumbsize_h;
            $width  = ($imagethumbsize_h / $height_orig) * $width_orig;
        }

        $thumb   = imagecreatetruecolor($width , $height);
        $bgcolor = imagecolorallocate($thumb, $red, $green, $blue);
        ImageFilledRectangle($thumb, 0, 0, $width, $height, $bgcolor);
        imagealphablending($thumb, true);

        imagecopyresampled($thumb, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        $thumb2 = imagecreatetruecolor($imagethumbsize_w , $imagethumbsize_h);
        // true color for better quality
        $bgcolor = imagecolorallocate($thumb2, $red, $green, $blue);
        ImageFilledRectangle($thumb2, 0, 0, $imagethumbsize_w, $imagethumbsize_h, $bgcolor);
        imagealphablending($thumb2, true);

        $w1 = ($width  / 2) - ($imagethumbsize_w / 2);
        $h1 = ($height / 2) - ($imagethumbsize_h  / 2);

        imagecopyresampled($thumb2, $thumb, 0, 0, $w1, $h1,$imagethumbsize_w, $imagethumbsize_h, $imagethumbsize_w, $imagethumbsize_h);

        // create the file
        switch($format) {
            case 'image/jpeg':
                imagejpeg($thumb2, $fileout, $quality);
                break;

            case 'image/gif';
                imagegif($thumb2, $fileout);
                break;

            case 'image/png':
                imagepng($thumb2, $fileout);
                break;
        }
	}


    function resize( $filein, $fileout, $imagethumbsize_w, $imagethumbsize_h, $red, $green, $blue, $quality )
    {
        // Cacul des nouvelles dimensions
        list($width_orig, $height_orig) = getimagesize($filein);

        if ( preg_match("/.jpg/i", "$filein") || preg_match("/.jpeg/i", "$filein") ) {
            $format = 'image/jpeg';
        }
        if ( preg_match("/.gif/i", "$filein") ) {
            $format = 'image/gif';
        }
        if ( preg_match("/.png/i", "$filein") ) {
            $format = 'image/png';
        }

        switch ( $format ) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($filein);
                break;
            case 'image/gif';
                $image = imagecreatefromgif($filein);
                break;
            case 'image/png':
                $image = imagecreatefrompng($filein);
                break;
        }

        $ratio_orig = $width_orig/$height_orig;

        if ($imagethumbsize_w/$imagethumbsize_h > $ratio_orig) {
            $imagethumbsize_w = $imagethumbsize_h*$ratio_orig;
        } else {
            $imagethumbsize_h = $imagethumbsize_w/$ratio_orig;
        }

        // Redimensionnement
        $thumb3  = imagecreatetruecolor($imagethumbsize_w, $imagethumbsize_h);
        $bgcolor = imagecolorallocate($thumb3, $red, $green, $blue);
        ImageFilledRectangle($thumb3, 0 ,0 ,$imagethumbsize_w,
            $imagethumbsize_h, $bgcolor);
        imagealphablending($thumb3, true);

        imagecopyresampled($thumb3, $image, 0, 0, 0, 0, $imagethumbsize_w,
            $imagethumbsize_h, $width_orig, $height_orig);

        switch ( $format ) {
            case 'image/jpeg':
                imagejpeg($thumb3, $fileout, $quality); // on cree le fichier
                break;
            case 'image/gif';
                imagegif($thumb3, $fileout); // on cree le fichier
                break;
            case 'image/png':
                imagepng($thumb3, $fileout); // on cree le fichier
                break;
        }
    }
	
	public static function config(){
		echo \GCore\Helpers\Html::formStart('action_config image_resize_action_config', 'image_resize_action_config__XNX_');
		?>
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-_XNX_" data-g-toggle="tab"><?php echo l_('CF_IMG_RES_GENERAL'); ?></a></li>
			<li><a href="#big-img-_XNX_" data-g-toggle="tab"><?php echo l_('CF_IMG_RES_BIG_IMG'); ?></a></li>
			<li><a href="#med-img-_XNX_" data-g-toggle="tab"><?php echo l_('CF_IMG_RES_MED_IMG'); ?></a></li>
			<li><a href="#small-img-_XNX_" data-g-toggle="tab"><?php echo l_('CF_IMG_RES_SMALL_IMG'); ?></a></li>
		</ul>
		<div class="tab-content">
			<div id="general-_XNX_" class="tab-pane active">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][photo]', array('type' => 'text', 'label' => l_('CF_IMG_RES_PHOTO'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_PHOTO_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][delete_original]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_DELETE_ORIG'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_IMG_RES_DELETE_ORIG_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][quality]', array('type' => 'text', 'label' => l_('CF_IMG_RES_QUALITY'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_QUALITY_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="big-img-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_directory]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_DIR'), 'class' => 'L', 'sublabel' => l_('CF_IMG_RES_IMG_DIR_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_use]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMAGE_USE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_IMG_RES_IMAGE_USE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_prefix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_PREFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_PREFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_suffix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_SUFFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_SUFFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_height]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_HEIGHT'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_HEIGHT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_width]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_WIDTH'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_WIDTH_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_r]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_R'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_R_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_g]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_G'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_G_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_b]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_B'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_B_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][big_image_method]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMG_METHOD'), 'options' => array(0 => l_('CF_IMG_RES_RESIZE'), 1 => l_('CF_IMG_RES_RESIZE_CROP')), 'sublabel' => l_('CF_IMG_RES_IMG_METHOD_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="med-img-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_directory]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_DIR'), 'class' => 'L', 'sublabel' => l_('CF_IMG_RES_IMG_DIR_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_use]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMAGE_USE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_IMG_RES_IMAGE_USE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_prefix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_PREFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_PREFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_suffix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_SUFFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_SUFFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_height]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_HEIGHT'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_HEIGHT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_width]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_WIDTH'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_WIDTH_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_r]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_R'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_R_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_g]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_G'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_G_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_b]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_B'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_B_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][med_image_method]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMG_METHOD'), 'options' => array(0 => l_('CF_IMG_RES_RESIZE'), 1 => l_('CF_IMG_RES_RESIZE_CROP')), 'sublabel' => l_('CF_IMG_RES_IMG_METHOD_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
			<div id="small-img-_XNX_" class="tab-pane">
			<?php
			echo \GCore\Helpers\Html::formSecStart();
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_directory]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_DIR'), 'class' => 'L', 'sublabel' => l_('CF_IMG_RES_IMG_DIR_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_use]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMAGE_USE'), 'options' => array(0 => l_('NO'), 1 => l_('YES')), 'sublabel' => l_('CF_IMG_RES_IMAGE_USE_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_prefix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_PREFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_PREFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_suffix]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_SUFFIX'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_SUFFIX_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_height]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_HEIGHT'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_HEIGHT_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_width]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_WIDTH'), 'class' => 'M', 'sublabel' => l_('CF_IMG_RES_IMG_WIDTH_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_r]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_R'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_R_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_g]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_G'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_G_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_b]', array('type' => 'text', 'label' => l_('CF_IMG_RES_IMG_B'), 'class' => 'SS', 'sublabel' => l_('CF_IMG_RES_IMG_B_DESC')));
			echo \GCore\Helpers\Html::formLine('Form[extras][actions_config][_XNX_][small_image_method]', array('type' => 'dropdown', 'label' => l_('CF_IMG_RES_IMG_METHOD'), 'options' => array(0 => l_('CF_IMG_RES_RESIZE'), 1 => l_('CF_IMG_RES_RESIZE_CROP')), 'sublabel' => l_('CF_IMG_RES_IMG_METHOD_DESC')));
			echo \GCore\Helpers\Html::formSecEnd();
			?>
			</div>
		</div>
		<?php
		echo \GCore\Helpers\Html::formEnd();
	}
}