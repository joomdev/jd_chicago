<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2SmartsliderBackendInstallController extends N2SmartSliderController
{

    public function initialize() {

    }

    public function actionIndex($secured = false) {
        if ($secured) {
            N2Loader::import('models.Install', 'smartslider');

            $installModel = new N2SmartsliderInstallModel();

            $installModel->install();
        }
    }
} 