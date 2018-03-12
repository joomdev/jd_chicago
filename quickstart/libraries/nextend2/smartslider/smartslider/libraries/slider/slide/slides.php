<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderSlides
{

    /**
     * @var N2SmartSlider
     */
    protected $slider;

    /**
     * @var N2SmartSliderSlide[]
     */
    protected $slides = array();

    /**
     * @var N2SmartSliderSlide[]
     */
    protected $allEnabledSlides = array();

    private $randomize = false;
    private $randomizeFirst = false;
    protected $maximumSlideCount = 100;

    public function __construct($slider) {
        $this->slider = $slider;

        $params                  = $slider->params;
        $this->randomize         = intval($params->get('randomize', 0));
        $this->randomizeFirst    = intval($params->get('randomizeFirst', 0));
        $this->maximumSlideCount = intval($params->get('maximumslidecount', '100'));
        $this->maximumSlideCount = 10000;
    
    }

    /**
     * @return mixed
     */
    public function getSlides($extend = array(), $dummy = false) {

        $this->loadSlides(isset($extend['slidesData']) ? $extend['slidesData'] : array(), $dummy);

        if (!$this->hasSlides()) {
            return array();
        }

        $this->makeSlides(isset($extend['generatorData']) ? $extend['generatorData'] : array());

        return $this->slides;
    }

    public function hasSlides() {
        //check slide number
        if (count($this->slides) === 0) {
            if (N2Platform::$isAdmin) {
                N2Message::error(n2_('0 slides available for this slider.'));
            }
            $this->slider->norender = true;
            return false;
        }
        return true;
    }

    public function makeSlides($extend = array()) {

        $slides = &$this->slides;

        for ($i = 0; $i < count($slides); $i++) {
            $slides[$i]->initGenerator($extend);
        }

        for ($i = count($slides) - 1; $i >= 0; $i--) {
            if ($slides[$i]->hasGenerator()) {
                array_splice($slides, $i, 1, $slides[$i]->expandSlide());
            }
        }

        for ($i = 0; $i < count($slides) && count($slides) > 1; $i++) {
            if ($slides[$i]->isStatic()) {
                $this->slider->addStaticSlide($slides[$i]);
                array_splice($slides, $i, 1);
                $i--;
            }
        }

        if ($this->randomize) {
            shuffle($slides);
        }
        if ($this->maximumSlideCount > 0) {
            array_splice($slides, $this->maximumSlideCount);
        }

        if ($this->randomizeFirst) {
            $this->slider->_activeSlide = mt_rand(0, count($slides) - 1);
        } else {
            for ($i = 0; $i < count($slides); $i++) {
                if ($slides[$i]->isFirst()) {
                    $this->slider->_activeSlide = $i;
                    break;
                }
            }
        }

        if (count($slides) == 1 && $this->slider->params->get('loop-single-slide', 0)) {
            $slides[1] = clone $slides[0];
        }

        for ($i = 0; $i < count($slides); $i++) {
            $slides[$i]->setIndex($i);
        }
    }

    protected function loadSlides($extend, $dummy) {
        $this->slider->_activeSlide = 0;

        $where = $this->slidesWhereQuery();

        N2Loader::import("models.Slides", "smartslider");
        $slidesModel = new N2SmartsliderSlidesModel();
        $slideRows   = $slidesModel->getAll($this->slider->sliderId, $where);

        if (isset($extend['add'])) {
            if (!is_array($slideRows)) {
                $slideRows = array();
            }
            array_push($slideRows, $extend['add']);
        }

        if (count($slideRows) == 0 && $dummy) {
            $images = array(
                '$ss$/admin/images/dummyslide.png',
                '$ss$/admin/images/dummyslide.png',
                '$ss$/admin/images/dummyslide.png',
            );
            for ($i = 0; $i <= 7; $i++) {
                $index               = $i % count($images);
                $slideRows[]         = $slidesModel->getRowFromPost($this->slider->sliderId, array(
                    'title'           => 'Dummy slide #' . $i,
                    'publish_up'      => '',
                    'publish_down'    => '',
                    'generator_id'    => 0,
                    'slide'           => '',
                    'description'     => '',
                    'thumbnail'       => $images[$index],
                    'published'       => 1,
                    'first'           => 0,
                    'backgroundImage' => $images[$index]
                ), false);
                $slideRows[$i]['id'] = $i;
            }
        }
        for ($i = 0; $i < count($slideRows); $i++) {
            if (isset($extend[$slideRows[$i]['id']])) {
                $slideRows[$i] = $extend[$slideRows[$i]['id']];
            }
            $slide = $this->createSlide($slideRows[$i]);
            if ($slide->isVisible()) {
                $this->slides[] = $slide;
            }
            $this->allEnabledSlides[$i] = $slide;
        }
    }

    protected function createSlide($slideRow) {
        return new N2SmartSliderSlide($this->slider, $slideRow);
    }

    protected function slidesWhereQuery() {
        return " AND published = 1 ";
    }

    public function getNextCacheRefresh() {
        $earlier = 2145916800;
        for ($i = 0; $i < count($this->allEnabledSlides); $i++) {
            $earlier = min($this->allEnabledSlides[$i]->nextCacheRefresh, $earlier);
        }
        return $earlier;
    }

    public function getDummySlides($count) {

    }
} 