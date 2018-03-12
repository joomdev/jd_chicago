<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php


class N2SmartsliderBackendSliderView extends N2ViewBase
{

    public function _renderSlider($sliderId, $responsive = 'auto') {

        $slider = new N2SmartSliderManager($sliderId, false, array(
            'disableResponsive'     => true,
            'addDummySlidesIfEmpty' => true
        ));
        echo $slider->render();
    }

    public function _renderSliderCached($sliderId, $responsive = 'auto') {

        $slider = new N2SmartSliderManager($sliderId, false, array(
            'disableResponsive' => true
        ));
        echo $slider->render(true);
    }

    public function renderForm($slider) {


        $values = N2SmartsliderSlidersModel::renderEditForm($slider);

        // Used by AJAX widget subforms
        N2JS::addFirstCode("
            new NextendForm(
                'smartslider-form',
                '" . $this->appType->router->createAjaxUrl(array(
                'slider/edit',
                array('sliderid' => $slider['id'])
            )) . "',
                " . json_encode($values) . "
            );
        ");

    }

    public function getDashboardButtons($slider) {
        $sliderid = $slider['id'];

        $app          = N2Base::getApplication('smartslider');
        $accessEdit   = N2Acl::canDo('smartslider_edit', $app->info);
        $accessDelete = N2Acl::canDo('smartslider_delete', $app->info);

        $buttons = '';

        if ($accessEdit) {
            $buttons .= NHtml::tag('a', array(
                'data-label' => n2_('Clear slider cache'),
                'href'       => $this->appType->router->createUrl(array(
                    'slider/clearcache',
                    array(
                        'sliderid' => $sliderid
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-i-a-refresh')));

            $buttons .= NHtml::tag('a', array(
                'data-label' => n2_('Export slider as HTML'),
                'href'       => $this->appType->router->createUrl(array(
                    'slider/exporthtml',
                    array(
                        'sliderid' => $sliderid,
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-i-a-html')));

            $buttons .= NHtml::tag('a', array(
                'data-label' => n2_('Export'),
                'href'       => $this->appType->router->createUrl(array(
                    'slider/export',
                    array(
                        'sliderid' => $sliderid,
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-i-a-export')));

            $buttons .= NHtml::tag('a', array(
                'data-label' => n2_('Duplicate slider'),
                'href'       => $this->appType->router->createUrl(array(
                    'slider/duplicate',
                    array(
                        'sliderid' => $sliderid,
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-i-a-duplicate')));

        }

        if ($accessDelete) {
            $buttons .= NHtml::tag('a', array(
                'data-label' => n2_('Delete slider'),
                "onclick"    => "return NextendDeleteModalLink(this, 'slider-delete', " . json_encode($slider['title']) . ");",
                'href'       => $this->appType->router->createUrl(array(
                    'slider/delete',
                    array(
                        'sliderid' => $sliderid,
                    ) + N2Form::tokenizeUrl()
                ))
            ), NHtml::tag('i', array('class' => 'n2-i n2-i-a-delete')));
        }

        return $buttons;
    }

} 