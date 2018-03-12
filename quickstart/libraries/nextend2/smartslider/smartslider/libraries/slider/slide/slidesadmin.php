<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.slider.slide.slides', 'smartslider');

class N2SmartSliderSlidesAdmin extends N2SmartSliderSlides
{

    protected function slidesWhereQuery() {
        $date = N2Platform::getDate();
        return "   AND ((published = 1 AND (publish_up = '0000-00-00 00:00:00' OR publish_up < '{$date}')
                   AND (publish_down = '0000-00-00 00:00:00' OR publish_down > '{$date}'))
                   OR id = " . N2Request::getInt('slideid') . ") ";
    }

    public function hasSlides() {
        return true;
    }

    protected function createSlide($slideRow) {
        return new N2SmartSliderSlideAdmin($this->slider, $slideRow);
    }

    public function makeSlides($extend = array()) {

        if (N2Request::getCmd('nextendcontroller') == 'slides') {

            $slides = &$this->slides;

            if (N2Request::getCmd('nextendaction') == 'create') {
                if ($this->maximumSlideCount > 0) {
                    array_splice($slides, $this->maximumSlideCount - 1);
                }

                $staticSlide = N2Request::getInt('static', 0);
                $slide       = $this->createSlide(array(
                    'id'           => 0,
                    'title'        => 'Title',
                    'slider'       => N2Request::getInt('sliderid'),
                    'publish_up'   => '0000-00-00 00:00:00',
                    'publish_down' => '0000-00-00 00:00:00',
                    'published'    => 1,
                    'first'        => 0,
                    'slide'        => '',
                    'description'  => '',
                    'thumbnail'    => '',
                    'background'   => 'ffffff00|*|',
                    'params'       => json_encode(array('static-slide' => $staticSlide)),
                    'ordering'     => count($slides),
                    'generator_id' => 0
                ));
                if ($slide->isStatic()) {
                    $this->slider->addStaticSlide($slide);
                    if (count($slides) == 0) {
                        $slide2 = $this->createSlide(array(
                            'id'           => 0,
                            'title'        => 'Title',
                            'slider'       => N2Request::getInt('sliderid'),
                            'publish_up'   => '0000-00-00 00:00:00',
                            'publish_down' => '0000-00-00 00:00:00',
                            'published'    => 1,
                            'first'        => 0,
                            'slide'        => '',
                            'description'  => '',
                            'thumbnail'    => '',
                            'background'   => 'ffffff00|*|',
                            'params'       => '',
                            'ordering'     => count($slides),
                            'generator_id' => 0
                        ));
                        array_push($slides, $slide2);
                    }
                } else {
                    for ($i = 0; $i < count($slides); $i++) {
                        if ($slides[$i]->isStatic()) {
                            $this->slider->addStaticSlide($slides[$i]);
                            array_splice($slides, $i, 1);
                            $i--;
                        }
                    }

                    array_push($slides, $slide);
                    $this->slider->_activeSlide = count($slides) - 1;
                }
            } else {

                $currentlyEdited      = N2Request::getInt('slideid');
                $currentlyEditedSlide = null;
                $isStatic             = false;

                for ($i = 0; $i < count($slides); $i++) {
                    if ($slides[$i]->isStatic()) {
                        if ($slides[$i]->id == $currentlyEdited) {
                            $isStatic = true;
                        }
                        $this->slider->addStaticSlide($slides[$i]);
                        array_splice($slides, $i, 1);
                        $i--;
                    }
                }

                if ($isStatic) {
                    for ($i = 0; $i < count($this->slider->staticSlides); $i++) {
                        if ($this->slider->staticSlides[$i]->id != $currentlyEdited) {
                            array_splice($this->slider->staticSlides, $i, 1);
                            $i--;
                        }
                    }
                }

                for ($i = 0; $i < count($slides); $i++) {
                    $slides[$i]->initGenerator($extend);
                }

                for ($i = count($slides) - 1; $i >= 0; $i--) {
                    if ($slides[$i]->hasGenerator()) {
                        array_splice($slides, $i, 1, $slides[$i]->expandSlide());
                    }
                }

                if (!$isStatic) {
                    for ($i = 0; $i < count($slides); $i++) {
                        if ($slides[$i]->id == $currentlyEdited) {
                            $this->slider->_activeSlide = $i;
                            $currentlyEditedSlide       = $slides[$i];
                            break;
                        }
                    }
                } else {
                    if (count($slides) == 0) {
                        if (count($slides) == 0) {
                            $slide2 = $this->createSlide(array(
                                'id'           => 0,
                                'title'        => 'Title',
                                'slider'       => N2Request::getInt('sliderid'),
                                'publish_up'   => '0000-00-00 00:00:00',
                                'publish_down' => '0000-00-00 00:00:00',
                                'published'    => 1,
                                'first'        => 0,
                                'slide'        => '',
                                'description'  => '',
                                'thumbnail'    => '',
                                'background'   => 'ffffff00|*|',
                                'params'       => '',
                                'ordering'     => count($slides),
                                'generator_id' => 0
                            ));
                            array_push($slides, $slide2);
                        }
                    }
                    $this->slider->_activeSlide = 0;
                    $currentlyEditedSlide       = $slides[0];
                }
                if ($this->maximumSlideCount > 0) {
                    array_splice($slides, $this->maximumSlideCount);
                    $found = false;
                    for ($i = 0; $i < count($slides); $i++) {
                        if ($slides[$i] == $currentlyEditedSlide) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        $this->slider->_activeSlide          = count($slides) - 1;
                        $slides[$this->slider->_activeSlide] = $currentlyEditedSlide;
                    }
                }
                if ($currentlyEditedSlide) {
                    $currentlyEditedSlide->setCurrentlyEdited();
                }
            }
        }
    }
}

class N2SmartSliderSlideAdmin extends N2SmartSliderSlide
{

    public function setSlidesParams() {
        $this->attributes['data-variables'] = json_encode($this->variables);
        parent::setSlidesParams();
    }

    protected function addSlideLink() {

    }

    public function isVisible() {
        return true;
    }

    protected function onCreate() {
    }
}