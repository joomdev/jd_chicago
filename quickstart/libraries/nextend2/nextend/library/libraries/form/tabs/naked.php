<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.tab');

class N2TabNaked extends N2Tab
{

    function decorateGroupStart() {

    }

    function decorateGroupEnd() {

    }

    function decorateTitle() {

    }

    function decorateElement(&$el, $out, $i) {

        echo $out[1];
    }

}