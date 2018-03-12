<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderFeatureLayerMode
{

    private $slider;

    public $playOnce = 0;

    public $playFirstLayer = 1;

    public $mode = 'skippable';

    public $inAnimation = 'mainInEnd';

    public function __construct($slider) {

        $this->slider = $slider;

        $this->playOnce = intval($slider->params->get('playonce', 0));

        $this->playFirstLayer = intval($slider->params->get('playfirstlayer', 1));

        switch ($slider->params->get('layer-animation-play-mode', 'skippable')) {
            case 'forced':
                $this->mode = 'forced';
                break;
            default:
                $this->mode = 'skippable';
        }

        switch ($slider->params->get('layer-animation-play-in', 'end')) {
            case 'end':
                $this->inAnimation = 'mainInEnd';
                break;
            default:
                $this->inAnimation = 'mainInStart';
        }
    }

    public function makeJavaScriptProperties(&$properties) {
        $properties['layerMode'] = array(
            'playOnce'       => $this->playOnce,
            'playFirstLayer' => $this->playFirstLayer,
            'mode'           => $this->mode,
            'inAnimation'    => $this->inAnimation
        );

        $params                 = $this->slider->params;
        $properties['parallax'] = array(
            'enabled'    => intval($params->get('parallax-enabled', 1)),
            'mobile'     => intval($params->get('parallax-enabled-mobile', 0)),
            'is3D'       => intval($params->get('parallax-3d', 0)),
            'animate'    => intval($params->get('parallax-animate', 1)),
            'horizontal' => $params->get('parallax-horizontal', 'mouse'),
            'vertical'   => $params->get('parallax-vertical', 'mouse'),
            'origin'     => $params->get('parallax-mouse-origin', 'slider'),
            'scrollmove' => $params->get('parallax-scroll-move', 'both')
        );
    }
}