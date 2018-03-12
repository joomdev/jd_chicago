<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderBackendSlidesController extends N2SmartSliderController
{

    public $layoutName = 'default';

    public function initialize() {
        parent::initialize();

        N2Loader::import(array(
            'models.Sliders',
            'models.generator',
            'models.Layouts',
            'models.Layers',
            'models.Item',
            'models.Slides'
        ), 'smartslider');

        N2Localization::addJS(array(
            'In animation',
            'Loop animation',
            'Out animation'
        ));
    }

    private function initAdminSlider() {
        $sliderManager = new N2SmartSliderManager(N2Get::getInt('sliderid'), true, array(
            'disableResponsive' => true
        ));
        $this->appType->app->set('sliderManager', $sliderManager);
    }

    public function actionCreate() {
        if ($this->validatePermission('smartslider_edit')) {

            $slidersModel = new N2SmartsliderSlidersModel();
            $sliderId     = N2Request::getInt('sliderid');
            $slider       = $slidersModel->get($sliderId);
            if ($this->validateDatabase($slider)) {
                $this->initAdminSlider();

                $this->addView("../../inline/_sidebar_slide", array(
                    "appObj" => $this,
                    "slider" => $slider
                ), "sidebar");
                $this->addView("edit", array(
                    "slidesModel" => new N2SmartsliderSlidesModel(),
                    "sliderId"    => $sliderId
                ));
                $this->render();

            }
        }
    }

    public function actionEdit() {
        if ($this->validatePermission('smartslider_edit')) {
            $slidersModel = new N2SmartsliderSlidersModel();
            $sliderId     = N2Request::getInt('sliderid');
            $slider       = $slidersModel->get($sliderId);
            if ($this->validateDatabase($slider)) {
                $slidesModel = new N2SmartsliderSlidesModel();
                if (!$slidesModel->get(N2Request::getInt('slideid'))) {
                    $this->redirect("sliders/index");
                }

                $this->initAdminSlider();

                $this->addView("../../inline/_sidebar_slide", array(
                    "appObj" => $this,
                    "slider" => $slider
                ), "sidebar");
                $this->addView("edit", array(
                    "slidesModel" => new N2SmartsliderSlidesModel(),
                    "sliderId"    => $sliderId
                ));
                $this->render();
            }
        }
    }


    public function actionDelete() {
        if ($this->validateToken() && $this->validatePermission('smartslider_delete')) {
            if ($slideId = N2Request::getInt('slideid')) {
                $slidesModel = new N2SmartsliderSlidesModel();
                $slidesModel->delete($slideId);
                $this->redirect(N2Request::getUrlReferrer());
            }
            $this->redirect(array("sliders/index"));
        }
    }

    public function actionDuplicate() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            if ($slideId = N2Request::getInt('slideid')) {
                $slidesModel = new N2SmartsliderSlidesModel();
                $newSlideId  = $slidesModel->duplicate($slideId);

                N2Message::success(n2_('Slide duplicated.'));

                $this->redirect(array(
                    "slides/edit",
                    array(
                        "sliderid" => N2Request::getInt("sliderid"),
                        "slideid"  => $newSlideId
                    )
                ));
            }
            $this->redirect(array("sliders/index"));
        }
    }

    public function actionFirst() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            if (($slideId = N2Request::getInt('slideid')) && ($sliderid = N2Request::getInt('sliderid'))) {
                $slidesModel = new N2SmartsliderSlidesModel();
                $slidesModel->first($sliderid, $slideId);
                $this->redirect(N2Request::getUrlReferrer());
            }
            $this->redirect(array("sliders/index"));
        }
    }

    public function actionPublish() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            if ($slideId = N2Request::getInt('slideid')) {
                $slidesModel = new N2SmartsliderSlidesModel();
                $slidesModel->publish($slideId);
                $this->redirect(N2Request::getUrlReferrer());
            }
            $this->redirect(array("sliders/index"));
        }
    }

    public function actionUnPublish() {
        if ($this->validateToken() && $this->validatePermission('smartslider_edit')) {
            if ($slideId = N2Request::getInt('slideid')) {
                $slidesModel = new N2SmartsliderSlidesModel();
                $slidesModel->unpublish($slideId);
                $this->redirect(N2Request::getUrlReferrer());
            }
            $this->redirect(array("sliders/index"));
        }
    }

} 