<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php
N2Form::importElement('hidden');
N2Loader::import('libraries.backgroundanimation.manager', 'smartslider');

class N2ElementBackgroundAnimation extends N2ElementHidden
{

    public $_tooltip = true;

    function fetchElement() {

        N2Localization::addJS(array(
            'Disabled',
            'Multiple animations',
            'Single animation'
        ));

        N2JS::addInline('new NextendElementAnimationManager("' . $this->_id . '", "backgroundanimationManager");');

        return NHtml::tag('div', array(
            'class' => 'n2-form-element-option-chooser n2-border-radius'
        ), parent::fetchElement() . NHtml::tag('input', array(
                'type'     => 'text',
                'class'    => 'n2-h5',
                'style'    => 'width: 130px;' . N2XmlHelper::getAttribute($this->_xml, 'css'),
                'disabled' => 'disabled'
            ), false) . NHtml::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-clear'
            ), NHtml::tag('i', array('class' => 'n2-i n2-it n2-i-empty n2-i-grey-opacity'), '')) . NHtml::tag('a', array(
                'href'  => '#',
                'class' => 'n2-form-element-button n2-h5 n2-uc'
            ), n2_('Animations')));
    }
}
