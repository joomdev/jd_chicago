<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Loader::import('libraries.slider.generator.abstract', 'smartslider');

class N2SmartSliderSlidesGenerator
{

    private static $localCache = array();

    /**
     * @var N2SmartSliderSlide
     */
    private $slide;

    private $generatorModel;

    public $currentGenerator;

    private $slider;

    /**
     * @param $slide N2SmartSliderSlide
     * @param $slider
     * @param $extend
     */
    public function __construct($slide, $slider, $extend) {
        N2Loader::import("libraries.slider.cache.generator", "smartslider");
        N2Loader::import("models.generator", "smartslider");

        $this->slide  = $slide;
        $this->slider = $slider;

        $this->generatorModel             = new N2SmartsliderGeneratorModel();
        $this->currentGenerator           = $this->generatorModel->get($this->slide->generator_id);
        $this->currentGenerator['params'] = new N2Data($this->currentGenerator['params'], true);

        if (isset($extend[$this->slide->generator_id])) {
            $extend = new N2Data($extend[$this->slide->generator_id]);
            $slide->parameters->set('record-slides', $extend->get('record-slides', 1));
            $extend->un_set('record-slides');
            $this->currentGenerator['params']->loadArray($extend->toArray());
        }
    }

    public function getSlides() {
        $slides = array();
        $data   = $this->getData();
        for ($i = 0; $i < count($data); $i++) {
            $newSlide = clone $this->slide;
            $newSlide->setVariables($data[$i]);
            $slides[] = $newSlide;
        }
        if (count($slides) == 0) {
            $slides[] = $this->slide;
        }
        return $slides;
    }

    public function fillSample() {
        $data = $this->getData();
        if (count($data) > 0) {
            $this->slide->setVariables($data[0]);
        }
    }

    private function getData() {
        if (!isset(self::$localCache[$this->slide->generator_id])) {
            $cache                                        = new N2CacheManifestGenerator($this->slider, $this);
            self::$localCache[$this->slide->generator_id] = $cache->makeCache('generator' . $this->currentGenerator['id'], md5(json_encode($this->currentGenerator) . max($this->slide->parameters->get('record-slides'), 1)), array(
                $this,
                'getNotCachedData'
            ));
        }

        return self::$localCache[$this->slide->generator_id];
    }

    public function getNotCachedData() {

        $info = $this->generatorModel->getGeneratorInfo($this->currentGenerator['group'], $this->currentGenerator['type']);

        require_once($info->path . '/generator.php');
        $class = 'N2Generator' . $this->currentGenerator['group'] . $this->currentGenerator['type'];
        /** @var N2GeneratorAbstract $dataSource */
        $dataSource = new $class($info, $this->currentGenerator['params']);

        return $dataSource->getData(max($this->slide->parameters->get('record-slides'), 1), max($this->currentGenerator['params']->get('record-start'), 1), $this->getSlideGroup());
    }

    public function setNextCacheRefresh($time) {
        $this->slide->setNextCacheRefresh($time);
    }

    public function getSlideCount() {
        return max($this->slide->parameters->get('record-slides'), 1);
    }

    public function getSlideGroup() {
        return max($this->currentGenerator['params']->get('record-group'), 1);
    }

    public function getSlideStat() {
        return count($this->getData()) . '/' . $this->getSlideCount();
    }
}