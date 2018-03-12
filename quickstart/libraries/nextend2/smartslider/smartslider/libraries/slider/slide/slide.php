<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartSliderSlide
{

    /**
     * @var N2SmartSliderAbstract
     */
    protected $sliderObject;
    public $id = 0, $slider = 0, $publish_up, $publish_down, $published = 1, $first = 0, $slide = '', $ordering = 0, $generator_id = 0;

    protected $title = '', $description = '', $thumbnail = '';

    public $parameters, $background = '';

    protected $active = false;

    protected $html = '';

    protected $visible = 1;

    protected $underEdit = false;

    /**
     * @var bool|N2SmartSliderSlidesGenerator
     */
    protected $generator = false;
    protected $variables = array();

    public $index = -1;

    public $attributes = array(), $containerAttributes = array(
        'class' => 'n2-ss-layers-container',
        'style' => ''
    ), $classes = '', $style = '';

    public $nextCacheRefresh = 2145916800; // 2038

    public function __construct($slider, $data) {
        $this->parameters = new N2Data($data['params'], true);
        unset($data['params']);
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        $this->sliderObject = $slider;
        $this->onCreate();
    }

    public function __clone() {
        $this->parameters = clone $this->parameters;
    }

    protected function onCreate() {
        N2Pluggable::doAction('ssSlide', array($this));
    }

    public function initGenerator($extend = array()) {
        if ($this->generator_id > 0) {
            $this->generator = new N2SmartSliderSlidesGenerator($this, $this->sliderObject, $extend);
        }
    }

    public function hasGenerator() {
        return !!$this->generator;
    }

    /**
     * @return N2SmartSliderSlide[]
     */
    public function expandSlide() {
        return $this->generator->getSlides();
    }

    public function fillSample() {
        if ($this->hasGenerator()) {
            $this->generator->fillSample();
        }
    }

    public function setVariables($variables) {
        $this->variables = array_merge($this->variables, (array)$variables);
    }

    public function isFirst() {
        return !!$this->first;
    }

    public function isActive() {
        return $this->active;
    }

    public function isCurrentlyEdited() {
        return N2Request::getInt('slideid') == $this->id;
    }

    public function setIndex($index) {
        $this->index = $index;
    }

    public function setActive() {
        $this->classes .= ' n2-ss-slide-active';
        $this->active = true;
    }

    public function prepare() {
        $this->variables['slide'] = array(
            'name'        => $this->getTitle(),
            'description' => $this->getDescription()
        );
    }

    public function setSlidesParams() {

        $this->background = $this->sliderObject->features->makeBackground($this);

        $this->addSlideLink();

        $this->attributes['data-slide-duration'] = floatval($this->parameters->get('slide-duration', 0) / 1000);
        $this->attributes['data-id']             = $this->id;

        $this->sliderObject->features->makeSlide($this);

        $this->renderHtml();
    }

    protected function addSlideLink() {
        list($url, $target) = (array)N2Parse::parse($this->parameters->getIfEmpty('link', '|*|'));

        if (!empty($url) && $url != '#') {

            if (empty($target)) {
                $target = '_self';
            }

            $url = $this->fill($url);

            $this->containerAttributes['onclick'] = '';
            if (strpos($url, 'javascript:') === 0) {
                $this->containerAttributes['onclick'] = $url;
            } else {

                N2Loader::import('libraries.link.link');
                $url = N2LinkParser::parse($url, $this->containerAttributes);

                $this->containerAttributes['data-href'] = (N2Platform::$isJoomla ? JRoute::_($url, false) : $url);
                if (empty($this->containerAttributes['onclick'])) {
                    if ($target == '_blank') {
                        $this->containerAttributes['n2click'] = "window.open(this.getAttribute('data-href'),'_blank');";
                    } else {
                        $this->containerAttributes['n2click'] = "window.location=this.getAttribute('data-href')";
                    }
                }
            }
            $this->containerAttributes['style'] .= 'cursor:pointer;';
        }
    }

    protected function renderHtml() {
        if (empty($this->html)) {

            $layerRenderer = new N2SmartSliderLayer($this->sliderObject, $this);

            $html   = '';
            $layers = json_decode($this->slide, true);
            if (!$this->underEdit) {
                $layers = N2SmartSliderLayer::translateIds($layers);
            }
            if (is_array($layers)) {
                foreach ($layers AS $layer) {
                    $html .= $layerRenderer->render($layer);
                }
            }
            $this->html = NHtml::tag('div', $this->containerAttributes, $html);
        }
    }

    public function getHTML() {
        return $this->html;
    }

    public function getAsStatic() {

        $layerRenderer = new N2SmartSliderLayer($this->sliderObject, $this);

        $html   = '';
        $layers = json_decode($this->slide, true);
        if (!$this->underEdit) {
            $layers = N2SmartSliderLayer::translateIds($layers);
        }
        if (is_array($layers)) {
            foreach ($layers AS $layer) {
                $html .= $layerRenderer->render($layer);
            }
        }
        return NHtml::tag('div', array('class' => 'n2-ss-static-slide'), $html);
    }

    public function isStatic() {
        if ($this->parameters->get('static-slide', 0)) {
            return true;
        }
        return false;
    }

    public function fill($value) {
        if (!empty($this->variables)) {
            return preg_replace_callback('/{((([a-z]+)\(([0-9a-zA-Z_,\/\(\)]+)\))|([a-zA-Z0-9_\/]+))}/', array(
                $this,
                'parseFunction'
            ), $value);
        }
        return $value;
    }

    private function parseFunction($match) {
        if (!isset($match[5])) {
            $args = preg_split('/,(?!.*\))/', $match[4]);
            for ($i = 0; $i < count($args); $i++) {
                $args[$i] = $this->parseVariable($args[$i]);
            }
            return call_user_func_array(array(
                $this,
                '__' . $match[3]
            ), $args);

        } else {
            return $this->parseVariable($match[5]);
        }
    }

    private function parseVariable($variable) {
        preg_match('/((([a-z]+)\(([0-9a-zA-Z_,\/\(\)]+)\)))/', $variable, $match);
        if (!empty($match)) {
            return call_user_func(array(
                $this,
                'parseFunction'
            ), $match);
        } else {
            preg_match('/([a-zA-Z][0-9a-zA-Z_]*)(\/([0-9a-z]+))?/', $variable, $match);
            if ($match) {
                $index = empty($match[3]) ? 0 : $match[3];
                if (is_numeric($index)) {
                    $index = max(1, intval($index)) - 1;
                }

                if (isset($this->variables[$index]) && isset($this->variables[$index][$match[1]])) {
                    return $this->variables[$index][$match[1]];
                } else {
                    return '';
                }
            }
            return $variable;
        }
    }

    private function __cleanhtml($s) {
        return strip_tags($s, '<p><a><b><br><br/><i>');
    }

    private function __removehtml($s) {
        return strip_tags($s);
    }

    private function __splitbychars($s, $start, $length) {
        return substr($s, $start, $length);
    }

    private function __splitbywords($s, $start, $length) {
        $len      = strlen($s);
        $posStart = max(0, $start == 0 ? 0 : strpos($s, ' ', $start));
        $posEnd   = max(0, $length > $len ? $len : strpos($s, ' ', $length));
        return substr($s, $posStart, $posEnd);
    }

    private function __findimage($s, $index) {
        $index = isset($index) ? intval($index) - 1 : 0;
        preg_match_all('/(<img.*?src=[\'"](.*?)[\'"][^>]*>)|(background(-image)??\s*?:.*?url\((["|\']?)?(.+?)(["|\']?)?\))/i', $s, $r);
        if (isset($r[2]) && !empty($r[2][$index])) {
            $s = $r[2][$index];
        } else if (isset($r[6]) && !empty($r[6][$index])) {
            $s = trim($r[6][$index], "'\" \t\n\r\0\x0B");
        } else {
            $s = '';
        }
        return $s;
    }

    private function __findlink($s, $index) {
        $index = isset($index) ? intval($index) - 1 : 0;
        preg_match_all('/href=["\']?([^"\'>]+)["\']?/i', $s, $r);
        if (isset($r[1]) && !empty($r[1][$index])) {
            $s = $r[1][$index];
        } else {
            $s = '';
        }
        return $s;
    }

    /*
    public function fill($value) {
        if (!empty($this->variables)) {
            return preg_replace_callback('/{(.*?)(\/([0-9]+))?}/', array(
                $this,
                'replaceVariable'
            ), $value);
        }
        return $value;
    }

    private function replaceVariable($match) {
        if (!isset($match[3])) {
            $match[3] = 1;
        }
        if ($this->variables[$match[3] - 1][$match[1]]) {
            return $this->variables[$match[3] - 1][$match[1]];
        }
        return '';
    }
    */

    public function getTitle() {
        return $this->fill($this->title);
    }

    public function getDescription() {
        return $this->fill($this->description);
    }

    public function getThumbnail() {
        $image = $this->thumbnail;
        if (empty($image)) {
            $image = $this->parameters->get('backgroundImage');
        }
        return N2ImageHelper::fixed($this->fill($image));
    }

    public function getRow() {
        $this->fillParameters();
        return array(
            'title'        => $this->getTitle(),
            'slide'        => $this->getFilledSlide(),
            'description'  => $this->getDescription(),
            'thumbnail'    => N2ImageHelper::dynamic($this->getThumbnail()),
            'published'    => $this->published,
            'publish_up'   => $this->publish_up,
            'publish_down' => $this->publish_down,
            'first'        => $this->first,
            'params'       => $this->parameters->toJSON(),
            'slider'       => $this->slider,
            'ordering'     => $this->ordering,
            'generator_id' => 0
        );
    }

    public function fillParameters() {
        $this->parameters->set('backgroundImage', $this->fill($this->parameters->get('backgroundImage')));
        $this->parameters->set('backgroundAlt', $this->fill($this->parameters->get('backgroundAlt')));
        $this->parameters->set('backgroundTitle', $this->fill($this->parameters->get('backgroundTitle')));
        $this->parameters->set('backgroundVideoMp4', $this->fill($this->parameters->get('backgroundVideoMp4')));
        $this->parameters->set('backgroundVideoWebm', $this->fill($this->parameters->get('backgroundVideoWebm')));
        $this->parameters->set('backgroundVideoOgg', $this->fill($this->parameters->get('backgroundVideoOgg')));
        $this->parameters->set('link', $this->fill($this->parameters->get('link')));
    }

    public function getFilledSlide() {
        $layerRenderer = new N2SmartSliderLayer($this->sliderObject, $this);

        $rawSlide = array();
        $layers   = json_decode($this->slide, true);
        if (!$this->underEdit) {
            $layers = N2SmartSliderLayer::translateIds($layers);
        }
        if (is_array($layers)) {
            foreach ($layers AS $layer) {
                $rawSlide[] = $layerRenderer->getFilled($layer);
            }
        }
        return json_encode($rawSlide);
    }

    public function setNextCacheRefresh($time) {
        $this->nextCacheRefresh = min($this->nextCacheRefresh, $time);
    }

    public function setVisibility($visibility) {
        $this->visible = $visibility;
    }

    public function isVisible() {

        if (!$this->visible) {
            return false;
        }

        $time = N2Platform::getTime();

        $publish_up   = strtotime($this->publish_up);
        $publish_down = strtotime($this->publish_down);

        if ($publish_down) {
            if ($publish_down > $time) {
                $this->setNextCacheRefresh($publish_down);
            } else {
                return false;
            }
        }

        if ($publish_up) {
            if ($publish_up > $time) {
                $this->setNextCacheRefresh($publish_up);
                return false;
            }
        }
        return true;
    }

    public function getSlideCount() {
        if ($this->hasGenerator()) {
            return $this->generator->getSlideCount();
        }
        return 1;
    }

    public function getSlideStat() {
        if ($this->hasGenerator()) {
            return $this->generator->getSlideStat();
        }
        return '1/1';
    }

    public function setCurrentlyEdited() {
        $this->underEdit = true;
    }
}

class N2SmartSliderSlideHelper
{

    public $data = array(
        'id'                     => 0,
        'title'                  => '',
        'publishdates'           => '|*|',
        'published'              => 1,
        'first'                  => 0,
        'slide'                  => array(),
        'description'            => '',
        'thumbnail'              => '',
        'ordering'               => 0,
        'generator_id'           => 0,
        "static-slide"           => 0,
        "backgroundColor"        => "ffffff00",
        "backgroundImage"        => "",
        "backgroundImageOpacity" => 100,
        "backgroundAlt"          => "",
        "backgroundTitle"          => "",
        "backgroundMode"         => "fill",
        "backgroundVideoMp4"     => "",
        "backgroundVideoWebm"    => "",
        "backgroundVideoOgg"     => "",
        "backgroundVideoMuted"   => 1,
        "backgroundVideoLoop"    => 1,
        "backgroundVideoMode"    => "fill",
        "link"                   => "|*|_self",
        "slide-duration"         => 0
    );

    public function __construct($properties = array()) {
        foreach ($properties as $k => $v) {
            $this->data[$k] = $v;
        }
    }

    public function set($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * @param $layer N2SmartSliderLayerHelper
     */
    public function addLayer($layer) {
        $this->data['slide'][] = &$layer->data;
        $layer->set('zIndex', count($this->data['slide']));
    }

    public function toArray() {
        return $this->data;
    }
}