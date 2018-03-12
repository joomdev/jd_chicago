<?php
/**
* @author    Roland Soos
* @copyright (C) 2015 Nextendweb.com
* @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die('Restricted access');
?><?php

class N2SmartsliderSettingsModel extends N2Model
{

    public function form($xml) {

        /** @noinspection PhpUnusedLocalVariableInspection */
        $data = array();
        switch ($xml) {
            case 'joomla':
                $data = N2SmartSliderJoomlaSettings::getAll();
                break;
            default:
                $data = N2SmartSliderSettings::getAll();
                break;
        }
        $this->render(dirname(__FILE__) . '/forms/settings/' . $xml . '.xml', $data);
    }

    public function render($xmlpath, $data) {
        N2Loader::import('libraries.form.form');
        $form = new N2Form(N2Base::getApplication('smartslider')->getApplicationType('backend'));

        $form->loadArray($data);

        $form->loadXMLFile($xmlpath);

        echo $form->render('settings');

        N2JS::addFirstCode('
            new NextendForm("smartslider-form", ' . json_encode($form->_data) . ', null, "' . N2Filesystem::toLinux(N2Filesystem::pathToRelativePath($xmlpath)) . '", "settings", "' . N2Uri::ajaxUri('nextend', 'smartslider') . '");
        ');
    }

    public function save() {
        $namespace = N2Request::getCmd('namespace', 'default');
        $settings  = N2Request::getVar('settings');
        if ($namespace && $settings) {
            if ($namespace == 'default') $namespace = 'settings';
            if ($namespace == 'font' && N2Request::getInt('sliderid')) {
                $namespace .= N2Request::getInt('sliderid');
                self::markChanged(N2Request::getInt('sliderid'));
            }
            if ($namespace == 'joomla') {
                $license = empty($settings['license']) ? '' : $settings['license'];

                $updates = $this->db->queryAll("SELECT b.update_site_id FROM " . $this->db->tableAlias("extensions") . " AS a LEFT JOIN " . $this->db->tableAlias("update_sites_extensions") . " AS b ON a.extension_id = b.extension_id WHERE a.element = 'com_smartslider3'");

                if (count($updates)) {
                    $id = $updates[0]['update_site_id'];
                    unset($updates[0]);
                    if (count($updates)) {
                        foreach ($updates AS $u) {
                            $this->db->setTableName("update_sites");
                            $this->db->deleteByAttributes(array(
                                "update_site_id" => $u['update_site_id']
                            ));
                            $this->db->setTableName("update_sites_extensions");
                            $this->db->deleteByAttributes(array(
                                "update_site_id" => $u['update_site_id']
                            ));
                        }
                    }
                    $this->db->setTableName("update_sites");
                    $this->db->update(array(
                        "location" => 'http://www.nextendweb.com/update2/joomla/update.php?license=' . urlencode($license) . '&fake=extension.xml',
                    ), array(
                        "update_site_id" => $id
                    ));
                }
            }
            N2SmartSliderSettings::store($namespace, json_encode($settings));
        }
    }

    public static function markChanged($id) {
        N2SmartSliderHelper::getInstance()->setSliderChanged($id, 1);
    }

    public function saveDefaults($defaults) {
        if (!empty($defaults)) {
            foreach ($defaults AS $referenceKey => $value) {
                N2StorageSectionAdmin::set('smartslider', 'default', $referenceKey, $value);
            }
        }
        return true;
    }

} 