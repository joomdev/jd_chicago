<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderFeatureFadeOnLoad
{

    private $slider;

    public $fadeOnLoad = 1;

    public $fadeOnScroll = 0;

    public $playWhenVisible = 1;

    public function __construct($slider) {

        $this->slider = $slider;

        $this->fadeOnLoad   = intval($slider->params->get('fadeOnLoad', 1));
        $this->fadeOnScroll = intval($slider->params->get('fadeOnScroll', 0));
        $this->playWhenVisible = intval($slider->params->get('playWhenVisible', 1));



        if (!empty($this->fadeOnScroll) && $this->fadeOnScroll) {
            $this->fadeOnLoad   = 1;
            $this->fadeOnScroll = 1;
        } else {
            $this->fadeOnScroll = 0;
        }
    }

    public function forceFadeOnLoad() {
        if (!$this->fadeOnScroll && !$this->fadeOnLoad) {
            $this->fadeOnLoad = 1;
        }
    }

    public function getSliderClass() {
        if ($this->fadeOnLoad) {
            return 'n2-ss-load-fade ';
        }
        return '';
    }

    public function renderPlaceholder($sizes) {

        if (!$this->slider->isAdmin && $this->fadeOnLoad && ($this->slider->features->responsive->scaleDown || $this->slider->features->responsive->scaleUp)) {

            if (N2SystemHelper::testMemoryLimit()) {
                if ($sizes['width'] + $sizes['marginHorizontal'] > 0 && $sizes['height'] > 0 && function_exists('imagecreatetruecolor')) {
                    return NHtml::tag("div", array(
                        "id"     => $this->slider->elementId . "-placeholder",
                        "encode" => false,
                        "style"  => 'position: relative;z-index:2;'
                    ), $this->makeImage($sizes));
                } else {
                    N2CSS::addCode("#{$this->slider->elementId} .n2-ss-load-fade{position: relative !important;}", $this->slider->cacheId);
                }

            } else {
                N2Message::error(n2_("It seems like the <a href='http://php.net/manual/en/ini.core.php#ini.memory-limit'>memory_limit</a> on the server is too low for the fade on load feature. Please set it minimum 60M and reload the page! You can disable this message in <a href='" . N2Form::$documentation . "#Troubleshooting-G-Server'>global configuration</a> 'Frontend debug message' option."));
            }
        } else {
            N2CSS::addCode("#{$this->slider->elementId}.n2-ss-load-fade{position: relative !important;}", $this->slider->cacheId);
        }
        return '';
    }

    public function makeJavaScriptProperties(&$properties) {
        $properties['load'] = array(
            'fade'   => $this->fadeOnLoad,
            'scroll' => ($this->fadeOnScroll & !$this->slider->isAdmin)
        );
        $properties['playWhenVisible'] = $this->playWhenVisible;
    }


    private function makeImage($sizes) {
        $html = NHtml::image("data:image/svg+xml;base64," . $this->transparentImage($sizes['width'] + $sizes['marginHorizontal'], $sizes['height']), '', array(
            'style' => 'width: 100%; max-width:' . ($this->slider->features->responsive->maximumSlideWidth + $sizes['marginHorizontal']) . 'px;'
        ));

        if ($sizes['marginVertical'] > 0) {
            $html .= NHtml::image("data:image/svg+xml;base64," . $this->transparentImage($sizes['width'] + $sizes['marginHorizontal'], $sizes['marginVertical']), '', array(
                'style' => 'width: 100%;'
            ));
        }

        return $html;
    }

    private function transparentImage($width, $height) {

        return base64_encode('<svg xmlns="http://www.w3.org/2000/svg" version="1.0" width="' . $width . '" height="' . $height . '" ></svg>');
    }

    private static function  gcd($a, $b) {
        return ($a % $b) ? self::gcd($b, $a % $b) : $b;
    }
}