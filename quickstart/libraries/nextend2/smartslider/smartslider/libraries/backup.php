<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderBackup
{

    public $NextendImageHelper_Export, $slider, $slides, $generators = array(), $NextendImageManager_ImageData = array(), $imageTranslation = array(), $visuals = array();

    public function __construct() {
        $this->NextendImageHelper_Export = N2ImageHelper::export();
    }
}