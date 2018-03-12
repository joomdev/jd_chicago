<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2SmartsliderBackendPreviewView extends N2ViewBase
{

    public function _renderSlider($sliderId, $extendSlider = array()) {
        $slider = new N2SmartSliderManager($sliderId, false, array(
            'disableResponsive'     => true,
            'extend'                => $extendSlider,
            'addDummySlidesIfEmpty' => true
        ));
        return $slider->render();
    }

} 