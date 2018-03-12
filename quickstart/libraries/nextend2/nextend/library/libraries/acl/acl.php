<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

abstract class N2AclAbstract
{

    public static $aclKey;

    public static function getInstance() {
        static $instance = null;
        if (null === $instance) {
            $instance = new N2Acl();
        }

        return $instance;
    }

    /**
     * @param                        $action
     * @param N2ApplicationInfo $info
     *
     * @return bool
     */
    public function authorise($action, $info) {
        return true;
    }

    public static function canDo($action, $info) {
        return self::getInstance()->authorise($action, $info);
    }
}

N2Loader::import('libraries.acl.acl', 'platform');