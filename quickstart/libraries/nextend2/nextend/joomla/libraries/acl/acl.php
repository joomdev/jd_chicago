<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2Acl extends N2AclAbstract
{

    private $user = null;

    public function __construct() {
        $this->user = JFactory::getUser();
    }

    public function authorise($action, $info) {
        if($action == $info->getName()){
            $action = 'core.manage';
        }
        return $this->user->authorise($action, $info->getAcl());
    }
}