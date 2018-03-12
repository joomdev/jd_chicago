<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2TopBar extends N2EmbedWidget implements N2EmbedWidgetInterface
{

    public static $params = array(
        'menu'         => array(),
        'actions'      => array(),
        'snapClass'    => 'n2-main-top-bar',
        'fixTo'        => true,
        'expert'       => true,
        'notification' => true,
        'hideSidebar'  => false,
        'back'         => false
    );

    public function run($params) {
        $params = array_merge(self::$params, $params);

        if (!$params['fixTo']) {
            $params['snapClass'] = '';
        }

        if (!is_array($params['actions'])) {
            $params['actions'] = array();
        }

        if (!$this->viewObject->appType->app->hasExpertMode()) {
            $params['expert'] = false;
        }

        $this->render($params);
    }

} 