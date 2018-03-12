<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.mobiledetect.Mobile_Detect');
N2Loader::import('libraries.parse.font');

N2Loader::import('libraries.slider.type', 'smartslider');
N2Loader::import('libraries.slider.css', 'smartslider');
N2Loader::importAll('libraries.slider.features', 'smartslider');
N2Loader::import('libraries.slider.javascript', 'smartslider');
N2Loader::importAll('libraries.slider.slide', 'smartslider');
N2Loader::import('libraries.settings.settings', 'smartslider');
N2Loader::import('libraries.slider.widget.widgets', 'smartslider');

abstract class N2SmartSliderAbstract
{

    public $sliderId = 0;

    public $elementId = '';

    public $cacheId = '';

    public $data;

    public $params;

    /**
     * @var N2SmartSliderFeatures
     */
    public $features;

    public $disableResponsive = false;

    protected $parameters = null;

    /**
     * @var N2SmartSliderSlides
     */
    public $slidesBuilder;

    /**
     * @var N2SmartSliderSlide[]
     */
    public $slides;

    public $isAdmin = false;

    public $_activeSlide = 0;
    /**
     * @var Mobile_Detect
     */
    protected $device;
    /**
     * @var NextendSmartSliderCSS
     */
    public $assets;
    protected $cache = false;

    public static $_identifier = 'n2-ss';

    public $fontSize = 16;

    /** @var N2SmartSliderSlide[] */
    public $staticSlides = array();

    /** @var  N2SmartSliderType */
    protected $sliderType;

    public $staticHtml = '';

    public $isStaticEdited = false;

    public function __construct($sliderId, $parameters) {

        $this->sliderId = $sliderId;

        $this->setElementId();

        if ($this->isAdmin) {
            $this->cacheId = self::getAdminCacheId($this->sliderId);
        } else {
            $this->cacheId = self::getCacheId($this->sliderId);
        }

        $this->parameters = array_merge(array(
            'extend'                => array(),
            'disableResponsive'     => false,
            'addDummySlidesIfEmpty' => false
        ), $parameters);

        $this->disableResponsive = $this->parameters['disableResponsive'];


        $this->device = new Mobile_Detect();

        N2Loader::import("models.Sliders", "smartslider");

    }

    public function setElementId() {
        $this->elementId = self::$_identifier . '-' . $this->sliderId;
    }

    public static function getCacheId($sliderId) {
        return self::$_identifier . '-' . $sliderId;
    }

    public static function getAdminCacheId($sliderId) {
        return self::$_identifier . '-admin-' . $sliderId;
    }


    public function getSliderTypeResource($resourceName) {

        $type = $this->data->get('type', 'simple');

        $class = 'N2SSPluginType' . $type;

        N2Loader::importPath(call_user_func(array(
                $class,
                "getPath"
            )) . NDS . $resourceName);

        $class = 'N2SmartSlider' . $resourceName . $type;
        return new $class($this);
    }

    abstract public function parseSlider($slider);

    abstract public function addCMSFunctions($slider);

    public function loadSlider() {

        $slidersModel = new N2SmartsliderSlidersModel();
        $slider       = $slidersModel->get($this->sliderId);
        if (empty($slider)) {
            return false;
        }
        if (isset($this->parameters['extend']['sliderData']) && is_array($this->parameters['extend']['sliderData'])) {
            $sliderData      = $this->parameters['extend']['sliderData'];
            $slider['title'] = $sliderData['title'];
            unset($sliderData['title']);
            $slider['type'] = $sliderData['type'];
            unset($sliderData['type']);

            $this->data   = new N2Data($slider);
            $this->params = new N2Data($sliderData);
        } else {
            $this->data   = new N2Data($slider);
            $this->params = new N2Data($slider['params'], true);
        }

        $this->sliderType = $this->getSliderTypeResource('type');
        $this->params->fillDefault($this->sliderType->getDefaults());
        $this->sliderType->limitParams($this->params);

        $this->features = new N2SmartSliderFeatures($this);

        $this->initSlides();
        return true;
    }

    private function initSlides() {
        if ($this->isAdmin) {
            $this->slidesBuilder = new N2SmartSliderSlidesAdmin($this);
        } else {
            $this->slidesBuilder = new N2SmartSliderSlides($this);
        }
        $this->slides = $this->slidesBuilder->getSlides(isset($this->parameters['extend']) ? $this->parameters['extend'] : array(), $this->parameters['addDummySlidesIfEmpty']);
    }

    public function render() {

        if (!$this->loadSlider()) {
            return false;
        }

        if (count($this->slides) == 0) {
            return false;
        }

        $this->assets = $this->getSliderTypeResource('css');
        $this->assets->render();
        $this->slides[$this->_activeSlide]->setActive();
        for ($i = 0; $i < count($this->slides); $i++) {
            $this->slides[$i]->prepare();
            $this->slides[$i]->setSlidesParams();
        }

        $this->renderStaticSlide();
        $slider = $this->sliderType->render();

        if (!$this->isAdmin) {
            N2Plugin::callPlugin('ssitem', 'onNextendSliderRender', array(
                &$slider,
                $this->elementId
            ));
        }


        $slider = str_replace('n2-ss-0', $this->elementId, $slider);

        $dependency = intval($this->params->get('dependency'));
        if (!N2Platform::$isAdmin && $dependency > 0) {
            $slider = '<script id="' . $this->elementId . '" data-dependency="' . $dependency . '" type="rocket/slider">' . str_replace(array(
                    '<script',
                    '</script'
                ), array(
                    '<_s_c_r_i_p_t',
                    '<_/_s_c_r_i_p_t'
                ), $slider) . '</script>';
        }

        $slider = $this->features->translateUrl->renderSlider($slider);

        $slider = $this->features->align->renderSlider($slider, $this->assets->sizes['width']);
        $slider = $this->features->margin->renderSlider($slider);

        $slider .= $this->features->fadeOnLoad->renderPlaceholder($this->assets->sizes);

        return "\n<!-- Nextend Smart Slider 3 #" . $this->sliderId . " - BEGIN -->\n" . $slider . "\n<!-- Nextend Smart Slider 3 #" . $this->sliderId . " - END -->\n";
    }

    public function addStaticSlide($slide) {
        $this->staticSlides[] = $slide;
    }

    public function renderStaticSlide() {
        $this->staticHtml = '';
        if (count($this->staticSlides)) {
            for ($i = 0; $i < count($this->staticSlides); $i++) {
                $this->staticHtml .= $this->staticSlides[$i]->getAsStatic();
            }
        }
    }

    /**
     * @return N2SmartSliderSlide
     */
    public function getPreviousSlide() {
        $length = count($this->slides);

        if ($this->_activeSlide == 0) {
            return $this->slides[$length - 1];
        }
        return $this->slides[$this->_activeSlide - 1];
    }

    /**
     * @return N2SmartSliderSlide
     */
    public function getNextSlide() {
        $length = count($this->slides);
        if ($this->_activeSlide == $length - 1) {
            return $this->slides[0];
        }
        return $this->slides[$this->_activeSlide + 1];
    }

    public static function removeShortcode($content) {
        $content = preg_replace('/smartslider3\[([0-9]+)\]/', '', $content);
        $content = preg_replace('/\[smartslider3 slider="([0-9]+)"\]/', '', $content);
        $content = preg_replace('/\[smartslider3 slider=([0-9]+)\]/', '', $content);
        return $content;
    }

    public function setStatic($isStaticEdited) {
        $this->isStaticEdited = $isStaticEdited;
    }
}

N2Loader::import("libraries.slider.slider", "smartslider.platform");