<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import('libraries.form.element.text');
N2Loader::import('libraries.browse.browse');

N2ImageHelper::init();

N2Loader::import('libraries.image.aviary');

class N2ElementImage extends N2ElementText
{

    protected $attributes = array();

    private $fixed = false;

    function fetchElement() {

        $fixed = N2XmlHelper::getAttribute($this->_xml, 'fixed');
        if ($fixed == '1') {
            $this->fixed = true;
        }

        N2ImageAviary::init();

        $html = parent::fetchElement();

        $params = array();

        N2ImageHelper::initLightbox();

        N2JS::addInline("new NextendElementImage('" . $this->_id . "', " . json_encode($params) . " );");

        if ($this->fixed) {

            $aviary = '';
            $html .= '<div id="' . $this->_id . '_preview" class="n2-form-element-preview n2-form-element-preview-fixed n2-border-radius" style="' . $this->getImageStyle() . '">
                ' . $aviary . '
            </div>';
        } else {

            $aviary = '';
            $html .= $aviary;
        }
        return $html;
    }

    protected function pre() {
        if (!$this->fixed) {
            return '<div id="' . $this->_id . '_preview" class="n2-form-element-preview n2-border-radius" style="' . $this->getImageStyle() . '"></div>';
        }
        return '';
    }

    protected function getImageStyle() {
        $image = $this->getValue();
        if (empty($image) || $image[0] == '{') {
            return '';
        }
        return 'background-image:url(' . N2ImageHelper::fixed($image) . ');';
    }

    protected function post() {
        return NHtml::tag('a', array(
            'href'  => '#',
            'class' => 'n2-form-element-clear'
        ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . '<a id="' . $this->_id . '_button" class="n2-form-element-button n2-h5 n2-uc" href="#">' . n2_('Image') . '</a>';
    }

    protected function getClass() {
        return 'n2-form-element-img ' . ($this->fixed ? 'n2-form-element-img-fixed ' : '');
    }
}
