<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2ViewBase
{

    /** @var  N2ApplicationType */
    public $appType;
    /** @var  N2EmbedWidget */
    public $widget;

    public function __construct($appType, $widget) {
        $this->appType = $appType;
        $this->widget  = $widget;
    }
}
