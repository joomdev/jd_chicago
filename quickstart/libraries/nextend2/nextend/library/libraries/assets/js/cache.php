<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2AssetsCacheJS extends N2AssetsCache
{

    public $outputFileType = "js";

    protected function createInlineCode($group, &$codes) {
        return N2AssetsJs::serveJquery(parent::createInlineCode($group, $codes));
    }
}