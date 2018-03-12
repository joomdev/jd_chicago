<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderApplicationTypeWidget extends N2ApplicationType
{

    public $type = "widget";

    public function __construct($app, $appTypePath) {
        parent::__construct($app, $appTypePath);

        N2AssetsManager::addCachedGroup('core');
        N2AssetsManager::addCachedGroup('smartslider');
    }

    protected function autoload() {
        N2Loader::import(array(
            'helpers.NHtml',
            'libraries.cache.NextendModuleCache',
            'libraries.embedwidget.embedwidget',
        ));

        N2Loader::import(array(
            'libraries.settings.settings',
            'libraries.settings.layout',
            'libraries.settings.stylemanager',
            'libraries.settings.font'
        ), 'smartslider');
    }
}

