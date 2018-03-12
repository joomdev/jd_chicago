<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderFeatureAutoplay
{

    private $slider;

    public $isEnabled = 0, $isStart = 0, $duration = 8000;
    public $interval = 0, $intervalModifier = 'loop', $intervalSlide = 'current';
    public $stopOnClick = 1, $stopOnMouseEnter = 1, $stopOnMediaStarted = 1;
    public $resumeOnMouseLeave = 0, $resumeOnMediaEnded = 1, $resumeOnSlideChanged = 0;


    public function __construct($slider) {

        $this->slider = $slider;
        $params       = $slider->params;

        $this->isEnabled = intval($params->get('autoplay', 0));
        $this->isStart   = intval($params->get('autoplayStart', 1));
        $this->duration  = intval($params->get('autoplayDuration', 8000));

        if ($this->duration < 1) {
            $this->duration = 1500;
        }


        list($this->interval, $this->intervalModifier, $this->intervalSlide) = (array)N2Parse::parse($slider->params->get('autoplayfinish', '0|*|loop|*|current'));
        $this->interval = intval($this->interval);

        $this->stopOnClick        = intval($params->get('autoplayStopClick', 1));
        $this->stopOnMouse        = $params->get('autoplayStopMouse', 'enter');
        $this->stopOnMediaStarted = intval($params->get('autoplayStopMedia', 1));


        $this->resumeOnClick      = $params->get('autoplayResumeClick', 0);
        $this->resumeOnMouse      = $params->get('autoplayResumeMouse', 0);
        $this->resumeOnMediaEnded = intval($params->get('autoplayResumeMedia', 1));

    }

    public function makeJavaScriptProperties(&$properties) {
        $autoplayToSlide = 0;

        switch ($this->intervalModifier) {
            case 'slide':
                $autoplayToSlide = $this->interval;
                if ($this->intervalSlide == 'next') {
                    $autoplayToSlide++;
                }
                break;
            default:
                $autoplayToSlide = $this->interval * count($this->slider->slides) - 1;
                if ($this->intervalSlide == 'next') {
                    $autoplayToSlide++;
                }
                break;
        }
        $properties['autoplay'] = array(
            'enabled'         => $this->isEnabled,
            'start'           => $this->isStart,
            'duration'        => $this->duration,
            'autoplayToSlide' => $autoplayToSlide,
            'pause'           => array(
                'click'        => $this->stopOnClick,
                'mouse'        => $this->stopOnMouse,
                'mediaStarted' => $this->stopOnMediaStarted
            ),
            'resume'          => array(
                'click'        => $this->resumeOnClick,
                'mouse'        => $this->resumeOnMouse,
                'mediaEnded'   => $this->resumeOnMediaEnded,
                'slidechanged' => $this->resumeOnSlideChanged
            )
        );
    }
}