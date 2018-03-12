<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2ImageManager
{

    /**
     * @var N2StorageImage
     */
    private static $model;

    public static $loaded = array();

    public static function init() {
        static $inited = false;
        if (!$inited) {

            self::$model = new N2StorageImage();

            N2Pluggable::addAction('afterApplicationContent', 'N2ImageManager::load');
            $inited = true;
        }
    }

    public static function load() {
        N2Base::getApplication('system')->getApplicationType('backend')->run(array(
            'useRequest' => false,
            'controller' => 'image',
            'action'     => 'index'
        ));
    }

    public static function getImageData($image, $read = false) {
        $visual = self::$model->getByImage($image);
        if (empty($visual)) {
            if ($read) {
                return false;
            } else {
                $id     = self::addImageData($image, N2StorageImage::$emptyImage);
                $visual = self::$model->getById($id);
            }
        }
        self::$loaded[] = $visual;
        return json_decode(base64_decode($visual['value']), true);
    }

    public static function addImageData($image, $value) {
        return self::$model->add($image, $value);
    }

    public static function setImageData($image, $value) {
        self::$model->setByImage($image, $value);
    }
}

N2ImageManager::init();

class N2StorageImage
{

    private $model = null;

    public static $emptyImage = array(
        'desktop' => array(
            'size' => '0|*|0'
        ),
        'tablet'  => array(
            'image' => '',
            'size'  => '0|*|0'
        ),
        'mobile'  => array(
            'image' => '',
            'size'  => '0|*|0'
        )
    );

    public function __construct() {
        $this->model = new N2Model("nextend2_image_storage");
    }

    public function getById($id) {
        return $this->model->db->findByAttributes(array(
            "id" => $id
        ));
    }

    public function getByImage($image) {
        return $this->model->db->findByAttributes(array(
            "hash" => md5($image)
        ));
    }

    public function setById($id, $value) {

        if (is_array($value)) {
            $value = base64_encode(json_encode($value));
        }

        $result = $this->getById($id);

        if ($result !== null) {
            $this->model->db->update(array('value' => $value), array(
                "id" => $id
            ));
            return true;
        }
        return false;
    }

    public function setByImage($image, $value) {

        if (is_array($value)) {
            $value = base64_encode(json_encode($value));
        }

        $result = $this->getByImage($image);

        if ($result !== null) {
            $this->model->db->update(array('value' => $value), array(
                "id" => $result['id']
            ));
            return true;
        }
        return false;
    }

    public function getAll() {
        return $this->model->db->findAllByAttributes(array(), array(
            "id",
            "hash",
            "image",
            "value"
        ));
    }

    public function set($image, $value) {

        if (is_array($value)) {
            $value = base64_encode(json_encode($value));
        }

        $result = $this->getByImage($image);

        if (empty($result)) {
            return $this->add($image, $value);
        } else {
            $attributes = array(
                "id" => $result['id']
            );
            $this->model->db->update(array('value' => $value), $attributes);
            return true;
        }
    }

    public function add($image, $value) {

        if (is_array($value)) {
            $value = base64_encode(json_encode($value));
        }

        $this->model->db->insert(array(
            "hash"  => md5($image),
            "image" => $image,
            "value" => $value
        ));
        return $this->model->db->insertId();
    }

    public function deleteById($id) {

        $this->model->db->deleteByAttributes(array(
            "id" => $id
        ));

        return true;
    }

    public function deleteByImage($image) {

        $this->model->db->deleteByAttributes(array(
            "hash" => md5($image)
        ));

        return true;
    }
}