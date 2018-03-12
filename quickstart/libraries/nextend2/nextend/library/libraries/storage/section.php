<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2StorageSectionAdmin
{

    /**
     * @var N2Model
     */
    public static $model;

    public static function get($application, $section, $referenceKey = null) {
        $attributes = array(
            "application" => $application,
            "section"     => $section
        );

        if ($referenceKey !== null) {
            $attributes['referencekey'] = $referenceKey;
        }

        return self::$model->db->findByAttributes($attributes);
    }

    public static function getById($id, $section = null) {
        $result = null;
        if ($section) {
            N2Pluggable::doAction($section, array(
                $id,
                &$result
            ));
            if ($result) {
                return $result;
            }
        }

        $result = self::$model->db->findByAttributes(array(
            "id" => $id
        ));
        if ($section && $result['section'] != $section) {
            return null;
        }
        return $result;
    }

    public static function getAll($application, $section, $referenceKey = null) {
        $attributes = array(
            "application" => $application,
            "section"     => $section
        );

        if ($referenceKey !== null) {
            $attributes['referencekey'] = $referenceKey;
        }

        $rows = self::$model->db->findAllByAttributes($attributes, array(
            "id",
            "referencekey",
            "value",
            "system",
            "editable"
        ));

        N2Pluggable::doAction($application . $section, array(
            $referenceKey,
            &$rows
        ));

        return $rows;
    }

    public static function add($application, $section, $referenceKey, $value, $system = 0, $editable = 1) {
        self::$model->db->insert(array(
            "application"  => $application,
            "section"      => $section,
            "referencekey" => $referenceKey,
            "value"        => $value,
            "system"       => $system,
            "editable"     => $editable
        ));
        return self::$model->db->insertId();
    }


    public static function set($application, $section, $referenceKey, $value, $system = 0, $editable = 1) {

        $result = self::getAll($application, $section, $referenceKey);

        if (empty($result)) {
            return self::add($application, $section, $referenceKey, $value, $system, $editable);
        } else {
            $attributes = array(
                "application" => $application,
                "section"     => $section
            );

            if ($referenceKey !== null) {
                $attributes['referencekey'] = $referenceKey;
            }
            self::$model->db->update(array('value' => $value), $attributes);
            return true;
        }
    }

    public static function setById($id, $value) {

        $result = self::getById($id);

        if ($result !== null && $result['editable']) {
            self::$model->db->update(array('value' => $value), array(
                "id" => $id
            ));
            return true;
        }
        return false;
    }

    public static function delete($application, $section, $referenceKey = null) {

        $attributes = array(
            "application" => $application,
            "section"     => $section,
            "system"      => 0
        );

        if ($referenceKey !== null) {
            $attributes['referencekey'] = $referenceKey;
        }

        self::$model->db->deleteByAttributes($attributes);
        return true;
    }

    public static function deleteById($id) {

        self::$model->db->deleteByAttributes(array(
            "id"     => $id,
            "system" => 0
        ));

        return true;
    }
}

N2StorageSectionAdmin::$model = new N2Model("nextend2_section_storage");

class N2StorageSection
{

    private $application = 'system';

    public function __construct($application) {
        $this->application = $application;
    }

    public function getById($id, $section) {
        return N2StorageSectionAdmin::getById($id, $section);
    }

    public function setById($id, $value) {
        return N2StorageSectionAdmin::setById($id, $value);
    }

    public function get($section, $referenceKey = null, $default = null) {
        $attributes = array(
            "application" => $this->application,
            "section"     => $section
        );

        if ($referenceKey !== null) {
            $attributes['referencekey'] = $referenceKey;
        }
        $result = N2StorageSectionAdmin::$model->db->findByAttributes($attributes);
        if (is_array($result)) {
            return $result['value'];
        }
        return $default;
    }

    public function getAll($section, $referenceKey = null) {
        return N2StorageSectionAdmin::getAll($this->application, $section, $referenceKey);
    }

    public function set($section, $referenceKey, $value) {
        N2StorageSectionAdmin::set($this->application, $section, $referenceKey, $value);
    }

    public function add($section, $referenceKey, $value) {
        return N2StorageSectionAdmin::add($this->application, $section, $referenceKey, $value);
    }

    public function deleteById($id) {
        return N2StorageSectionAdmin::deleteById($id);
    }
}