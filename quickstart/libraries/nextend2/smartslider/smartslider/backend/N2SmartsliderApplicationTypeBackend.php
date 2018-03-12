<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderApplicationTypeBackend extends N2ApplicationType
{

    public $type = "backend";

    protected function autoload() {
        N2Loader::import(array(
            'libraries.embedwidget.embedwidget',
            'libraries.plugin.plugin',
            'libraries.form.form',
            'libraries.image.color',
            'libraries.mobiledetect.Mobile_Detect',
            'libraries.parse.parse'
        ));

        N2Loader::import(array(
            'libraries.settings.settings',
            'libraries.settings.layout',
            'libraries.settings.stylemanager',
            'libraries.settings.font',
            'libraries.language'
        ), 'smartslider');

        N2Loader::import('helpers.controller.N2SmartSliderController', 'smartslider.backend');
    }

}