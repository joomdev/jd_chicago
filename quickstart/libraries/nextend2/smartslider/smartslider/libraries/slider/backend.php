<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import("libraries.slider.abstract", "smartslider");

class N2SmartSliderBackend extends N2SmartSlider
{

    public $isAdmin = true;

    public function setElementId() {
        $this->elementId = self::$_identifier . '-' . 0;
    }

    public function setCacheId() {
        $this->cacheId = self::$_identifier . '-' . $this->sliderId . '-backend';
    }
}