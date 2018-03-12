<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderBackendSettingsControllerAjax extends N2SmartSliderControllerAjax
{

    public function actionRated() {
        $this->validateToken();
        $this->appType->app->storage->set('free', 'rated', 1);
        $this->response->respond();
    }
}