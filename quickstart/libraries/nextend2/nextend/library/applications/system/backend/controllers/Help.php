<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemBackendHelpController extends N2BackendController
{
    public $layoutName = 'full';

    public function actionIndex() {
        $this->addView("index");
        $this->render();
    }

} 