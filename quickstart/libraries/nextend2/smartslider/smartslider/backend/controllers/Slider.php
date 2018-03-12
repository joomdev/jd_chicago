<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderBackendSliderController extends N2SmartSliderController
{

    public $sliderId = 0;

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Sliders',
            'models.Slides',
            'models.generator'
        ), 'smartslider');

        $this->sliderId = N2Request::getInt('sliderid');
    }

    public function actionClearCache() {
        if ($this->validateToken()) {
            $slidersModel = new N2SmartsliderSlidersModel();
            $slider       = $slidersModel->get($this->sliderId);
            if ($this->validateDatabase($slider)) {

                $slidersModel->refreshCache($this->sliderId);
                N2Message::success(n2_('Cache cleared.'));
                $this->redirect(array(
                    "slider/edit",
                    array("sliderid" => $this->sliderId)
                ));
            }
        }
    }

    public function actionCachedSlider() {
        if ($this->validateToken()) {
            $slidersModel = new N2SmartsliderSlidersModel();
            $slider       = $slidersModel->get($this->sliderId);
            if ($this->validateDatabase($slider)) {

                $this->addView('cachedslider', array(
                    'slider' => $slider
                ));
                $this->render();

            }
        }
    }

    public function actionEdit() {

        if ($this->validatePermission('smartslider_edit')) {

            $slidersModel = new N2SmartsliderSlidersModel();

            $slider = $slidersModel->get($this->sliderId);

            if (!$slider) {
                $this->redirectToSliders();
            }

            N2Loader::import('libraries.fonts.fontmanager');
            N2Loader::import('libraries.stylemanager.stylemanager');

            $this->addView("edit", array(
                'slider' => $slider
            ));

            $this->render();

        }
    }

    public function actionDelete() {
        if ($this->validateToken() && $this->validatePermission('smartslider_delete')) {
            $slidersModel = new N2SmartsliderSlidersModel();
            $slidersModel->delete($this->sliderId);
            N2Message::success(n2_('Slider deleted.'));
            $this->redirectToSliders();
        }
    }

    public function actionDuplicate() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            $slidersModel = new N2SmartsliderSlidersModel();
            if (($sliderid = N2Request::getInt('sliderid')) && $slidersModel->get($sliderid)) {
                $newSliderId = $slidersModel->duplicate($sliderid);
                N2Message::success(n2_('Slider duplicated.'));
                $this->redirect(array(
                    "slider/edit",
                    array("sliderid" => $newSliderId)
                ));
            }
            $this->redirectToSliders();
        }
    }

    public function actionExport() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            N2Loader::import('libraries.export', 'smartslider');
            $export = new N2SmartSliderExport($this->sliderId);
            $export->create();
        }
    }

    public function actionExportHTML() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            N2Loader::import('libraries.export', 'smartslider');
            $export = new N2SmartSliderExport($this->sliderId);
            $export->createHTML();
        }
    }

    public function actionPublishHTML() {
    }

}