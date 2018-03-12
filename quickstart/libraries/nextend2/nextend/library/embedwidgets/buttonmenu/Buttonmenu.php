<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ButtonMenu extends N2EmbedWidget implements N2EmbedWidgetInterface
{

    public static $params = array(
        'content' => '',
    );

    public function run($params) {
        $params = array_merge(self::$params, $params);
        $this->render($params);
        self::initOnce();
    }

    private static function initOnce() {
        static $init;
        if (!$init) {
            N2JS::addInline('$(".n2-button-menu-open").n2opener();');
            $init = true;
        }
    }
} 