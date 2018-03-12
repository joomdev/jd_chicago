<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import("smartslider3", "smartslider");

class N2SmartSliderApplication extends N2Application
{

    public $name = "smartslider";

    protected function autoload() {
        N2Loader::import("libraries.slider.helper", "smartslider");
        N2Loader::import("libraries.slider.manager", "smartslider");
        N2Form::$importPaths[] = dirname(__FILE__) . '/form';

        N2Filesystem::registerTranslate(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugins', $this->info->getAssetsPath() . '/plugins');
        require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'loadplugin.php';

        N2Loader::import('plugins.loadplugin', 'smartslider.platform');


        N2Loader::import('libraries.link', 'smartslider');
    }

    public function hasExpertMode() {
        return !!N2SSPRO;
    }
}