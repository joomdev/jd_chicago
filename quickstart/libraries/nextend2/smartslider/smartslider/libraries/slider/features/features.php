<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.image.image');
N2Loader::import('libraries.image.manager');

class N2SmartSliderFeatures
{

    private $slider;

    /**
     * @var N2SmartSliderFeatureFadeOnLoad
     */
    public $fadeOnLoad;

    /**
     * @var N2SmartSliderFeatureResponsive
     */
    public $responsive;

    /**
     * @var N2SmartSliderFeatureControls
     */
    public $controls;

    /**
     * @var N2SmartSliderFeatureLazyLoad
     */
    public $lazyLoad;

    /**
     * @var N2SmartSliderFeatureAlign
     */
    public $align;

    /**
     * @var N2SmartSliderFeatureBlockRightClick
     */
    public $blockRightClick;
    /**
     * @var N2SmartSliderFeatureAutoplay
     */
    public $autoplay;

    /**
     * @var N2SmartSliderFeatureTranslateUrl
     */
    public $translateUrl;

    /**
     * @var N2SmartSliderFeatureLayerMode
     */
    public $layerMode;

    /**
     * @var N2SmartSliderFeatureSlideBackground
     */
    public $slideBackground;

    public $slideBackgroundVideo;

    /**
     * @var N2SmartSliderFeaturePostBackgroundAnimation
     */
    public $postBackgroundAnimation;

    /**
     * @var N2SmartSliderFeatureSpinner
     */
    public $loadSpinner;

    private $initCallbacks = array();

    public function __construct($slider) {
        $this->slider = $slider;

        $this->fadeOnLoad      = new N2SmartSliderFeatureFadeOnLoad($slider);
        $this->responsive      = new N2SmartSliderFeatureResponsive($slider);
        $this->controls        = new N2SmartSliderFeatureControls($slider);
        $this->lazyLoad        = new N2SmartSliderFeatureLazyLoad($slider);
        $this->margin          = new N2SmartSliderFeatureMargin($slider);
        $this->align           = new N2SmartSliderFeatureAlign($slider);
        $this->blockRightClick = new N2SmartSliderFeatureBlockRightClick($slider);
        $this->maintainSession = new N2SmartSliderFeatureMaintainSession($slider);
        $this->autoplay        = new N2SmartSliderFeatureAutoplay($slider);
        $this->translateUrl    = new N2SmartSliderFeatureTranslateUrl($slider);
        $this->layerMode       = new N2SmartSliderFeatureLayerMode($slider);
        $this->slideBackground = new N2SmartSliderFeatureSlideBackground($slider);
        $this->loadSpinner = new N2SmartSliderFeatureSpinner($slider);
    }

    public function generateJSProperties() {

        $return = array(
            'admin'          => $this->slider->isAdmin,
            'isStaticEdited' => intval($this->slider->isStaticEdited),
            'translate3d'    => intval(N2SmartSliderSettings::get('hardware-acceleration', 1)),
            'randomize'      => intval(!$this->slider->isAdmin && $this->slider->params->get('randomize', 0)),
            'callbacks'      => $this->slider->params->get('callbacks', '')
        );

        $this->makeJavaScriptProperties($return);

        return $return;
    }

    protected function makeJavaScriptProperties(&$properties) {
        $this->fadeOnLoad->makeJavaScriptProperties($properties);
        $this->responsive->makeJavaScriptProperties($properties);
        $this->controls->makeJavaScriptProperties($properties);
        $this->lazyLoad->makeJavaScriptProperties($properties);
        $this->blockRightClick->makeJavaScriptProperties($properties);
        $this->maintainSession->makeJavaScriptProperties($properties);
        $this->autoplay->makeJavaScriptProperties($properties);
        $this->layerMode->makeJavaScriptProperties($properties);
        $this->loadSpinner->makeJavaScriptProperties($properties);
        $properties['initCallbacks'] = $this->initCallbacks;
    }

    /**
     * @param $slide N2SmartSliderSlide
     */
    public function makeSlide($slide) {
    }

    /**
     * @param $slide N2SmartSliderSlide
     *
     * @return string
     */
    public function makeBackground($slide) {

        $background = $this->slideBackground->make($slide);

        return $background;
    }

    protected function setDevices() {

        if (intval($this->_data->get('showmobile', 1)) == 0) {
            if (!$this->device->isTablet() && $this->device->isMobile()) {
                $this->norender = true;
                return;
            }
        }

        $custommobile = N2Parse::parse($this->_data->get('showcustommobile', '0|*|'));
        if ($custommobile[0] == 1) {
            if (!$this->device->isTablet() && $this->device->isMobile()) {
                $this->_data->set('slider', $custommobile[1]);
            }
        }

        if (intval($this->_data->get('showtablet', 1)) == 0) {
            if ($this->device->isTablet()) {
                $this->norender = true;
                return;
            }
        }

        $customtablet = N2Parse::parse($this->_data->get('showcustomtablet', '0|*|'));
        if ($customtablet[0] == 1) {
            if ($this->device->isTablet()) {
                $this->_data->set('slider', $customtablet[1]);
            }
        }

    }

    public function addInitCallback($callback) {
        $this->initCallbacks[] = $callback;
    }
}