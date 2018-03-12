<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderManager
{

    protected $usage = 'Unknown';

    public $slider;

    public function __construct($sliderId, $backend = false, $parameters = array()) {


        if ($backend) {
            N2Loader::import("libraries.slider.backend", "smartslider");
            $this->slider = new N2SmartSliderBackend($sliderId, $parameters);
        } else {
            N2Loader::import("libraries.slider.abstract", "smartslider");
            $this->slider = new N2SmartSlider($sliderId, $parameters);
        }

        N2AssetsManager::addCachedGroup($this->slider->cacheId);
    }

    public function setUsage($usage) {
        $this->usage = $usage;
    }

    public function getSlider() {
        return $this->slider;
    }

    public function render($cache = false) {
        if (!$cache) {
            return $this->slider->render();
        }
        N2Loader::import("libraries.slider.cache.slider", "smartslider");

        return $this->slider->addCMSFunctions($this->cacheSlider());
    }

    private function cacheSlider() {
        $cache        = new N2CacheManifestSlider($this->slider->cacheId, array(
            'slider' => $this->slider
        ));
        $cachedSlider = $cache->makeCache('slider', '', array(
            $this,
            'renderCachedSlider'
        ));

        if ($cachedSlider === false) {
            return '<h3>Smart Slider with ID #' . $this->slider->sliderId . ' does NOT EXIST or has NO SLIDES!</h3><h4>Usage: ' . $this->usage . '</h4>';
        }
        N2AssetsManager::loadFromArray($cachedSlider['assets']);

        return $cachedSlider['html'];
    }

    public function renderCachedSlider() {
        N2AssetsManager::createStack();

        $content         = array();
        $content['html'] = $this->slider->render();

        $assets = N2AssetsManager::removeStack();

        if ($content['html'] === false) {
            return false;
        }

        $content['assets'] = $assets;

        return $content;
    }
}