<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

N2Loader::import("libraries.slider.abstract", "smartslider");

class N2SmartsliderSlidersModel extends N2Model
{

    public function __construct() {
        parent::__construct("nextend2_smartslider3_sliders");
    }

    public function get($id) {
        return $this->db->queryRow("SELECT * FROM " . $this->db->tableName . " WHERE id = :id", array(
            ":id" => $id
        ));
    }

    public function refreshCache($sliderid) {
        N2Cache::clearGroup(N2SmartSliderAbstract::getCacheId($sliderid));
        N2Cache::clearGroup(N2SmartSliderAbstract::getAdminCacheId($sliderid));
        self::markChanged($sliderid);
    }


    /**
     * @return mixed
     */
    public function getAll($orderBy = 'time', $orderByDirection = 'DESC') {
        return $this->db->findAll($orderBy . ' ' . $orderByDirection);
    }

    public static function renderAddForm($data = array()) {
        return self::editForm($data);
    }

    public static function renderEditForm($slider) {

        $data = json_decode($slider['params'], true);
        if ($data == null) $data = array();
        $data['title'] = $slider['title'];
        $data['type']  = $slider['type'];
        return self::editForm($data);
    }

    private static function editForm($data = array()) {

        $configurationXmlFile = dirname(__FILE__) . '/forms/slider.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')
                                 ->getApplicationType('backend'));
        $form->set('class', 'nextend-smart-slider-admin');

        $form->loadArray($data);

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('slider');

        return $data;
    }

    public static function renderImportByUploadForm() {

        $configurationXmlFile = dirname(__FILE__) . '/forms/import/upload.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')
                                 ->getApplicationType('backend'));

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('slider');
    }

    public static function renderRestoreByUploadForm() {

        $configurationXmlFile = dirname(__FILE__) . '/forms/import/restore.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')
                                 ->getApplicationType('backend'));

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('slider');
    }

    public static function renderImportFromServerForm() {

        $configurationXmlFile = dirname(__FILE__) . '/forms/import/server.xml';

        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')
                                 ->getApplicationType('backend'));

        $form->loadXMLFile($configurationXmlFile);

        echo $form->render('slider');
    }

    function import($slider) {
        try {
            $this->db->insert(array(
                'title'  => $slider['title'],
                'type'   => $slider['type'],
                'params' => $slider['params']->toJSON(),
                'time'   => date('Y-m-d H:i:s', N2Platform::getTime())
            ));

            return $this->db->insertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    function restore($slider) {

        if (isset($slider['id']) && $slider['id'] > 0) {

            $this->delete($slider['id']);

            try {
                $this->db->insert(array(
                    'id'     => $slider['id'],
                    'title'  => $slider['title'],
                    'type'   => $slider['type'],
                    'params' => $slider['params']->toJSON(),
                    'time'   => date('Y-m-d H:i:s', N2Platform::getTime())
                ));

                return $this->db->insertId();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return $this->import($slider);
    }

    /**
     * @param $sliderId
     * @param $params N2Data
     */
    function importUpdate($sliderId, $params) {

        $this->db->update(array(
            'params' => $params->toJson()
        ), array(
            "id" => $sliderId
        ));
    }

    function create($slider) {
        if (!isset($slider['title'])) return false;
        if ($slider['title'] == '') $slider['title'] = n2_('New slider');

        $title = $slider['title'];
        unset($slider['title']);
        $type = $slider['type'];
        unset($slider['type']);

        try {
            $this->db->insert(array(
                'title'  => $title,
                'type'   => $type,
                'params' => json_encode($slider),
                'time'   => date('Y-m-d H:i:s', N2Platform::getTime())
            ));

            return $this->db->insertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    function save($id, $slider) {
        if (!isset($slider['title']) || $id <= 0) return false;
        if ($slider['title'] == '') $slider['title'] = n2_('New slider');

        $title = $slider['title'];
        unset($slider['title']);
        $type = $slider['type'];
        unset($slider['type']);

        $this->db->update(array(
            'title'  => $title,
            'type'   => $type,
            'params' => json_encode($slider)
        ), array(
            "id" => $id
        ));

        self::markChanged($id);

        return $id;
    }

    function delete($id) {
        $slidesModel = new N2SmartsliderSlidesModel();
        $slidesModel->deleteBySlider($id);

        $this->db->deleteByPk($id);

        N2Cache::clearGroup(N2SmartSliderAbstract::getCacheId($id));
        N2Cache::clearGroup(N2SmartSliderAbstract::getAdminCacheId($id));

        self::markChanged($id);
    }

    function deleteSlides($id) {
        $slidesModel = new N2SmartsliderSlidesModel();
        $slidesModel->deleteBySlider($id);
        self::markChanged($id);
    }

    function duplicate($id) {
        $slider = $this->get($id);
        unset($slider['id']);

        $slider['title'] .= ' - copy';
        $slider['time'] = date('Y-m-d H:i:s', N2Platform::getTime());

        try {
            $this->db->insert($slider);
            $newSliderId = $this->db->insertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$newSliderId) {
            return false;
        }

        $slidesModel = new N2SmartsliderSlidesModel();

        foreach ($slidesModel->getAll($id) AS $slide) {
            $slidesModel->copy($slide['id'], $newSliderId);
        }

        return $newSliderId;

    }

    function redirectToCreate() {
        N2Request::redirect($this->appType->router->createUrl(array("sliders/create")), 302, true);
    }

    function exportSlider($id) {

    }

    function exportSliderAsHTML($id) {

    }

    public static function markChanged($sliderid) {
        N2SmartSliderHelper::getInstance()
                           ->setSliderChanged($sliderid, 1);
    }
} 