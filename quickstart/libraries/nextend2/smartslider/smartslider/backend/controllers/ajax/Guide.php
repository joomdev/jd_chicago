<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderBackendGuideControllerAjax extends N2SmartSliderControllerAjax
{

    public function actionEnd() {

        $this->validateToken();

        $this->validatePermission('smartslider_edit');

        $key = N2Request::getCmd('key');
        N2SmartSliderSettings::set('guide-' . $key, 0);

        N2Message::notice('The ' . $key . ' guide completed. If you need it again, you can turn it on in the "Settings"!');

        $this->response->respond();
    }
}