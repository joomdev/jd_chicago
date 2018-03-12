<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderWidgetHomeController extends N2Controller
{

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Sliders',
            'models.Slides'
        ), 'smartslider');

    }

    public function actionIndex() {

    }

    public function actionJoomla($sliderid, $usage) {
        $this->addView("joomla", array(
            "sliderid" => $sliderid,
            "usage"    => $usage
        ), "content");

        $this->render();
    
    }

    public function actionWordpress($sliderid, $usage) {
    }

    public function actionMagento($sliderid, $usage) {
    }

    public function actionNative($sliderid, $usage) {
    }

} 