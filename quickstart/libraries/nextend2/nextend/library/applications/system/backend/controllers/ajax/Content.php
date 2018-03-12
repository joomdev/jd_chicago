<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemBackendContentControllerAjax extends N2BackendControllerAjax
{

    public function actionSearch() {
        $this->validateToken();
        N2Loader::import('libraries.models.content', 'platform');

        $keyword = N2Request::getVar('keyword', '');
        $this->response->respond(N2ModelsContent::search($keyword));
    }
}