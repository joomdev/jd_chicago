<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.form.tab');

class N2TabDarkRaw extends N2Tab
{

    function decorateGroupStart() {

    }

    function decorateGroupEnd() {

        echo "</div>";
    }

    function decorateElement(&$el, $out, $i) {

        echo $out[1];
    }

}