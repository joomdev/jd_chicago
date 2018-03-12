<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.list');

class N2ElementJqueryEasing extends N2ElementList
{

    function fetchElement() {

        $easings = array(
            "linear"           => "Linear",
            "easeInQuad"       => "Quad In",
            "easeOutQuad"      => "Quad Out",
            "easeInOutQuad"    => "Quad In Out",
            "easeInCubic"      => "Cubic In",
            "easeOutCubic"     => "Cubic Out",
            "easeInOutCubic"   => "Cubic In Out",
            "easeInQuart"      => "Quart In",
            "easeOutQuart"     => "Quart Out",
            "easeInOutQuart"   => "Quart In Out",
            "easeInQuint"      => "Quint In",
            "easeOutQuint"     => "Quint Out",
            "easeInOutQuint"   => "Quint In Out",
            "easeInSine"       => "Sine In",
            "easeOutSine"      => "Sine Out",
            "easeInOutSine"    => "Sine In Out",
            "easeInExpo"       => "Expo In",
            "easeOutExpo"      => "Expo Out",
            "easeInOutExpo"    => "Expo In Out",
            "easeInCirc"       => "Circ In",
            "easeOutCirc"      => "Circ Out",
            "easeInOutCirc"    => "Circ In Out",
            "easeInElastic"    => "Elastic In",
            "easeOutElastic"   => "Elastic Out",
            "easeInOutElastic" => "Elastic In Out",
            "easeInBack"       => "Back In",
            "easeOutBack"      => "Back Out",
            "easeInOutBack"    => "Back In Out",
            "easeInBounce"     => "Bounce In",
            "easeOutBounce"    => "Bounce Out",
            "easeInOutBounce"  => "Bounce In Out"
        );
        foreach ($easings as $k => $easing) {
            $this->_xml->addChild('option', ucfirst($easing))->addAttribute('value', $k);
        }
        return parent::fetchElement();
    }

}
