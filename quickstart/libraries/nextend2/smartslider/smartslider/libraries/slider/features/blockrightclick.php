<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderFeatureBlockRightClick
{

    private $slider;

    public $isEnabled = 0;

    public function __construct($slider) {

        $this->slider = $slider;

        $this->isEnabled = intval($slider->params->get('blockrightclick', 0));
    }

    public function makeJavaScriptProperties(&$properties) {

        $properties['blockrightclick'] = $this->isEnabled;
    }
}