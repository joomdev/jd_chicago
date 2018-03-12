<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SystemApplication extends N2Application
{

    public $name = "system";

    protected function autoload() {
        N2Loader::import(array(
            'libraries.embedwidget.embedwidget',
            'libraries.form.form'
        ));
    }

}
