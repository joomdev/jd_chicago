<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Nav extends N2EmbedWidget implements N2EmbedWidgetInterface
{

    public static $params = array(
        'logoUrl'      => false,
        'logoImageUrl' => false,
        'views'        => array(),
        'actions'      => array()
    );

    public function run($params) {
        $params = array_merge(self::$params, $params);

        $this->render($params);
    }

} 