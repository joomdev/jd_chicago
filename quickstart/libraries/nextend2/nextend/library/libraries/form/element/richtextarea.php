<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ElementRichTextarea extends N2Element
{

    function fetchElement() {

        N2JS::addInline('new NextendElementRichText("' . $this->_id . '");');

        $tools = array(
            NHtml::tag('div', array('class' => 'n2-textarea-rich-bold'), NHtml::tag('I', array('class' => 'n2-i n2-it n2-i-bold'))),
            NHtml::tag('div', array('class' => 'n2-textarea-rich-italic'), NHtml::tag('I', array('class' => 'n2-i n2-it n2-i-italic'))),
            NHtml::tag('div', array('class' => 'n2-textarea-rich-link'), NHtml::tag('I', array('class' => 'n2-i n2-it n2-i-link'))),
            //NHtml::tag('div', array('class' => 'n2-textarea-rich-list'), NHtml::tag('I', array('class' => 'n2-i n2-it n2-i-list')))
        );
        $rich  = NHtml::tag('div', array('class' => 'n2-textarea-rich'), implode('', $tools));

        return NHtml::tag('div', array(
            'class' => 'n2-form-element-textarea n2-form-element-rich-textarea n2-border-radius',
            'style' => N2XmlHelper::getAttribute($this->_xml, 'style')
        ), $rich . NHtml::tag('textarea', array(
                'id'           => $this->_id,
                'name'         => $this->_inputname,
                'class'        => 'n2 - h5',
                'autocomplete' => 'off',
                'style'        => N2XmlHelper::getAttribute($this->_xml, 'style2')
            ), $this->_form->get($this->_name, $this->_default)));
    }
}
